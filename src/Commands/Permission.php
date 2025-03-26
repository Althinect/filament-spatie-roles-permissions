<?php

namespace Althinect\FilamentSpatieRolesPermissions\Commands;

use ReflectionClass;
use ReflectionException;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Permission extends Command
{
    private mixed $config;

    private array $permissions = [];

    private array $policies = [];

    protected $signature = 'permissions:sync
                                {--C|clean}
                                {--P|policies}
                                {--O|oep}
                                {--Y|yes-to-all}
                                {--H|hard}';

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

        // Only attempt to delete existing permissions if a deletion option is passed
        if ($this->option('hard') || $this->option('clean')) {
            $deletionResult = $this->deleteExistingPermissions();
            if (!$deletionResult) {
                $this->line('<bg=yellow;options=bold;>*** OPERATION ABORTED ***</>');
                $this->warn('No changes were made.');
                $this->info('Consider running <bg=blue>php artisan permissions:sync</> if you just wish to sync without deleting.');
                return;
            }
        }

        $this->prepareClassPermissionsAndPolicies($classes);

        $this->prepareCustomPermissions();

        $permissionModel = config('permission.models.permission');

        $count = 0;

        foreach ($this->permissions as $permission) {
            $this->comment('Syncing Permission for: ' . $permission['name']);
            $permissionModel::firstOrCreate($permission);
            $count++;
        }

        $this->info('<bg=green;options=bold;>DONE</>');
        $this->info($count . ' permissions synced successfully.');
    }

    public function deleteExistingPermissions(): bool
    {
        $permissionsTable = config('permission.table_names.permissions');

        if ($this->option('hard')) {
            if ($this->option('yes-to-all') || $this->confirm("This will delete all existing permissions AND truncate your {$permissionsTable} database. Do you want to continue?", false)) {
                $this->comment('Deleting Permissions And Truncating');
                try {
                    Schema::disableForeignKeyConstraints();
                    DB::table($permissionsTable)->truncate();
                    Schema::enableForeignKeyConstraints();
                    return true;
                } catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                    return false;
                }
            }
        } elseif ($this->option('clean')) {
            if ($this->option('yes-to-all') || $this->confirm('This will delete all existing permissions. Do you want to continue?', false)) {
                $this->comment('Deleting Permissions');
                try {
                    DB::table($permissionsTable)->delete();
                    return true;
                } catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                    return false;
                }
            }
        }
        return false;
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

            $contents = $filesystem->get($this->getStub());

            foreach ($this->permissionAffixes() as $key => $permissionAffix) {
                foreach ($this->guardNames() as $guardName) {

                    $permission = eval($this->config['permission_name']);
                    $this->permissions[] = [
                        'name' => $permission,
                        'guard_name' => $guardName,
                    ];

                    if ($this->option('policies')) {
                        $contents = Str::replace('{{ ' . $key . ' }}', $permission, $contents);
                    }
                }
            }

            if ($this->option('policies') || $this->option('yes-to-all')) {

                $user = $this->config['user_model_class'];

                $policyVariables = [
                    'class' => $modelName . 'Policy',
                    'namespacedModel' => $model->getName(),
                    'namespacedUserModel' => (new ReflectionClass($this->config['user_model']))->getName(),
                    'namespace' => $this->config['policies_namespace'],
                    'user' => $user,
                    'model' => $modelName,
                    'modelVariable' => $modelName == 'User' ? 'model' : Str::lower($modelName),
                ];

                foreach ($policyVariables as $search => $replace) {
                    if ($modelName == $user && $search == 'namespacedModel') {
                        $contents = Str::replace('use {{ namespacedModel }};', '', $contents);
                    } else {
                        $contents = Str::replace('{{ ' . $search . ' }}', $replace, $contents);
                    }
                }

                if ($filesystem->exists(app_path('Policies/' . $modelName . 'Policy.php'))) {
                    if ($this->option('oep')) {
                        $filesystem->put(app_path('Policies/' . $modelName . 'Policy.php'), $contents);
                        $this->comment('Overriding Existing Policy: ' . $modelName);
                    } else {
                        $this->warn('Policy already exists for: ' . $modelName);
                    }
                } else {
                    $filesystem->put(app_path('Policies/' . $modelName . 'Policy.php'), $contents);
                    $this->comment('Creating Policy: ' . $modelName);
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
                $reflection = new ReflectionClass($resourceNameSpace . '\\' . $resource->getFilenameWithoutExtension());
                if (
                    ! $reflection->isAbstract() && $reflection->getParentClass() &&
                    $reflection->getParentClass()->getName() == 'Filament\Resources\Resource'
                ) {
                    $models[] = new ReflectionClass(app($resourceNameSpace . '\\' . $resource->getFilenameWithoutExtension())->getModel());
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
            $class = $namespace . '\\' . $file->getFilenameWithoutExtension();
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

    private function extractNamespace($file): string
    {
        $ns = '';
        $handle = fopen($file, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (preg_match('/namespace\s+([a-zA-Z0-9_\\\\]+);/', $line, $matches)) {
                    $ns = $matches[1];
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
        $excludedModels = $this->getExcludedModels();

        return array_diff(array_merge($models, $customModels), $excludedModels);
    }

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/genericPolicy.stub');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }
}
