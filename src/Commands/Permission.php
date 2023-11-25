<?php

namespace Althinect\FilamentSpatieRolesPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use Spatie\Permission\Models\Permission as PermissionModel;

class Permission extends Command
{
    private mixed $config;

    private array $permissions = [];

    private array $policies = [];

    protected $signature = 'permissions:sync 
                                {--C|clean} 
                                {--P|policies}
                                {--O|oep}
                                {--Y|yes-to-all}';

    protected $description = 'Generates permissions through Models or Filament Resources and custom permissions';

    public function __construct()
    {
        parent::__construct();
        $this->config = config('filament-spatie-roles-permissions.generator');
    }

    /**
     * @throws ReflectionException
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $classes = $this->getAllModels();

        $classes = array_diff($classes, $this->getExcludedModels());

        $this->deleteExistingPermissions();

        $this->prepareClassPermissionsAndPolicies($classes);

        $this->prepareCustomPermissions();

        foreach ($this->permissions as $permission) {
            $this->comment('Syncing Permission for: '.$permission['name']);
            PermissionModel::firstOrCreate($permission);
        }
    }

    public function deleteExistingPermissions(): void
    {
        if ($this->option('clean')) {
            if ($this->option('yes-to-all') || $this->confirm('This will delete existing permissions. Do you want to continue?', false)) {
                $this->comment('Deleting Permissions');
                try {
                    DB::table(config('permission.table_names.permissions'))->delete();
                    $this->comment('Deleted Permissions');
                } catch (\Exception $exception) {
                    $this->warn($exception->getMessage());
                }
            }
        }
    }

    /**
     * @throws ReflectionException
     * @throws FileNotFoundException
     */
    public function prepareClassPermissionsAndPolicies($classes): void
    {
        $filesystem = new Filesystem();

        // Ensure the policies folder exists
        File::ensureDirectoryExists(app_path('Policies/'));

        foreach ($classes as $model) {
            $modelName = $model->getShortName();

            $stub = '/stubs/genericPolicy.stub';
            $contents = $filesystem->get(__DIR__.$stub);

            foreach ($this->permissionAffixes() as $key => $permissionAffix) {
                foreach ($this->guardNames() as $guardName) {

                    $permission = eval($this->config['permission_name']);
                    $this->permissions[] = [
                        'name' => $permission,
                        'guard_name' => $guardName,
                    ];

                    if ($this->option('policies')) {
                        $contents = Str::replace('{{ '.$key.' }}', $permission, $contents);
                    }
                }
            }

            if ($this->option('policies') || $this->option('yes-to-all')) {

                $policyVariables = [
                    'class' => $modelName.'Policy',
                    'namespacedModel' => $model->getName(),
                    'namespacedUserModel' => (new ReflectionClass($this->config['user_model']))->getName(),
                    'namespace' => $this->config['policies_namespace'],
                    'user' => 'User',
                    'model' => $modelName,
                    'modelVariable' => $modelName == 'User' ? 'model' : Str::lower($modelName),
                ];

                foreach ($policyVariables as $search => $replace) {
                    if ($modelName == 'User' && $search == 'namespacedModel') {
                        $contents = Str::replace('use {{ namespacedModel }};', '', $contents);
                    } else {
                        $contents = Str::replace('{{ '.$search.' }}', $replace, $contents);
                    }
                }

                if ($filesystem->exists(app_path('Policies/'.$modelName.'Policy.php'))) {
                    if ($this->option('oep')) {
                        $filesystem->put(app_path('Policies/'.$modelName.'Policy.php'), $contents);
                        $this->comment('Overriding Existing Policy: '.$modelName);
                    } else {
                        $this->warn('Policy already exists for: '.$modelName);
                    }
                } else {
                    $filesystem->put(app_path('Policies/'.$modelName.'Policy.php'), $contents);
                    $this->comment('Creating Policy: '.$modelName);
                }
            }
        }
    }

    public function prepareCustomPermissions(): void
    {
        foreach ($this->getCustomPermissions() as $customPermission) {
            foreach ($this->guardNames() as $guardName) {
                $this->permissions[] = [
                    'name' => $customPermission,
                    'guard_name' => $guardName,
                ];
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    public function getModels(): array
    {
        $models = [];

        if ($this->config['discover_models_through_filament_resources']) {
            $resources = File::allFiles(app_path('Filament/Resources'));

            foreach ($resources as $resource) {
                $resourceNameSpace = $this->extractNamespace($resource);
                $reflection = new ReflectionClass($resourceNameSpace.'\\'.$resource->getFilenameWithoutExtension());
                if (
                    ! $reflection->isAbstract() && $reflection->getParentClass() &&
                    $reflection->getParentClass()->getName() == 'Filament\Resources\Resource'
                ) {
                    $models[] = new ReflectionClass(app($resourceNameSpace.'\\'.$resource->getFilenameWithoutExtension())->getModel());
                }
            }
        }

        foreach ($this->config['model_directories'] as $directory) {
            $models = array_merge($models, $this->getClassesInDirectory($directory));
        }

        return $models;
    }

    /**
     * @throws ReflectionException
     */
    private function getClassesInDirectory($path): array
    {
        $files = File::files($path);
        $models = [];

        foreach ($files as $file) {
            $namespace = $this->extractNamespace($file);
            $class = $namespace.'\\'.$file->getFilenameWithoutExtension();
            $model = new ReflectionClass($class);
            if (! $model->isAbstract()) {
                $models[] = $model;
            }

        }

        return $models;
    }

    private function permissionAffixes(): array
    {
        return $this->config['permission_affixes'];
    }

    private function guardNames(): array
    {
        return $this->config['guard_names'];
    }

    private function getCustomModels(): array
    {
        return $this->getModelReflections($this->config['custom_models']);
    }

    private function getCustomPermissions(): array
    {
        return $this->config['custom_permissions'];
    }

    private function getExcludedModels(): array
    {
        return $this->getModelReflections($this->config['excluded_models']);
    }

    private function getModelReflections($array): array
    {
        return array_map(function ($classes) {
            return new \ReflectionClass($classes);
        }, $array);
    }

    private function extractNamespace($file)
    {

        $ns = null;
        $handle = fopen($file, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (str_starts_with($line, 'namespace')) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }

        return $ns;
    }

    public function getAllModels(): array
    {
        $models = $this->getModels();
        $customModels = $this->getCustomModels();

        return array_merge($models, $customModels);
    }
}
