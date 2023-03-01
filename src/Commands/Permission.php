<?php

namespace Althinect\FilamentSpatieRolesPermissions\Commands;

use Althinect\FilamentSpatieRolesPermissions\Commands\Concerns\ManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission as PermissionModel;
use Illuminate\Support\Facades\DB;

class Permission extends Command
{
    private $config;

    private array $permissions = [];

    private array $policies = [];

    protected $signature = 'permissions:sync 
                                {--C|clean} 
                                {--P|policies}
                                {--Y|yes-to-all}';

    protected $description = 'Generates permissions through Models or Filament Resources and custom permissions';

    public function __construct()
    {
        parent::__construct();
        $this->config = config('filament-spatie-roles-permissions.generator');
    }

    public function handle(): void
    {
        $customModels = $this->getCustomModels();

        $models = $this->getModels();

        $classes = array_merge($customModels, $models);

        $classes = array_diff($classes, $this->getExcludedModels());

        $this->deleteExistingPermissions();

        $this->prepareClassPermissionsAndPolicies($classes);

        $this->prepareCustomPermissions();

        foreach ($this->permissions as $permission) {
            $this->comment("Syncing Permission for: " . $permission['name']);
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

    public function prepareClassPermissionsAndPolicies($classes): void
    {
        $filesystem = new Filesystem();

        $createPolicies = false;

        if ($this->option('policies')) {
            if (
                $this->option('yes-to-all') ||
                $this->confirm('This will override existing policy classes with the same name. Do you want to continue?', false)
            ) {
                $createPolicies = true;
            }
        }

        foreach ($classes as $model) {
            $modelName = $model->getShortName();

            $stub = '/stubs/genericPolicy.stub';
            $contents = $filesystem->get(__DIR__ . $stub);

            foreach ($this->permissionAffixes() as $key => $permissionAffix) {
                foreach ($this->guardNames() as $guardName) {

                    $permission = eval($this->config['permission_name']);
                    $this->permissions[] = [
                        'name' => $permission,
                        'guard_name' => $guardName
                    ];

                    if ($this->option('policies')) {
                        $contents = Str::replace("{{ " . $key . " }}", $permission, $contents);
                    }
                }
            }

            if (($this->option('policies') && $createPolicies) || $this->option('yes-to-all')) {

                $policyVariables = [
                    'class' => $modelName . 'Policy',
                    'namespacedModel' => $model->getName(),
                    'namespacedUserModel' => (new \ReflectionClass($this->config['user_model']))->getName(),
                    'namespace' => $this->config['policies_namespace'],
                    'user' => 'User',
                    'model' => $modelName,
                    'modelVariable' => $modelName == 'User' ? 'model' : Str::lower($modelName)
                ];

                foreach ($policyVariables as $search => $replace) {
                    if ($modelName == 'User' && $search == 'namespacedModel') {
                        $contents = Str::replace("use {{ namespacedModel }};", '', $contents);
                    } else {
                        $contents = Str::replace("{{ " . $search . " }}", $replace, $contents);
                    }
                }

                $filesystem->put(app_path('Policies/' . $modelName . 'Policy.php'), $contents);
                $this->comment('Creating Policy: ' . $modelName);
            }
        }
    }

    public function prepareCustomPermissions(): void
    {
        foreach ($this->getCustomPermissions() as $customPermission) {
            foreach ($this->guardNames() as $guardName) {
                $this->permissions[] = [
                    'name' => $customPermission,
                    'guard_name' => $guardName
                ];
            }
        }
    }

    public function getModels(): array
    {
        $models = [];

        if ($this->config['discover_models_through_filament_resources']) {
            $resources = File::files(app_path('../app/Filament/Resources'));

            foreach ($resources as $resource) {
                $resourceClass = $resource->getFilenameWithoutExtension();
                $models[] = class_basename(app('App\Filament\Resources\\' . $resourceClass)->getModel());
            }

            return $models;
        }

        foreach ($this->config['model_directories'] as $modelDirectory => $modelNamespace) {
            $models = array_merge($models, $this->getClassesInDirectory($modelDirectory, $modelNamespace));
        }

        return $models;
    }

    private function getClassesInDirectory($path, $namespace): array
    {
        $files = File::files($path);
        $models = [];

        foreach ($files as $file) {
            $class = new ($namespace . '\\' . $file->getFilenameWithoutExtension());
            $model = new \ReflectionClass($class);
            $models[] = $model;
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
}
