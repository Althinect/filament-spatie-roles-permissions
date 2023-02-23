<?php

namespace Althinect\FilamentSpatieRolesPermissions\Commands;

use Illuminate\Console\Command;
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

    protected $signature = 'permissions:sync {--C|clean}';

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

        $this->prepareClassPermissions($classes);

        $this->prepareCustomPermissions();

        foreach ($this->permissions as $permission) {
            $this->comment("Syncing Permission for: " . $permission['name']);
            PermissionModel::firstOrCreate($permission);
        }
    }

    public function deleteExistingPermissions(): void
    {
        if ($this->option('clean')) {
            $this->confirm('This will delete existing permissions. Do you want to continue?', false);
            $this->comment('Deleting Permissions');
            try {
                DB::table(config('permission.table_names.permissions'))->delete();
                $this->comment('Deleted Permissions');
            } catch (\Exception $exception) {
                $this->warn($exception->getMessage());
            }
        }
    }

    public function prepareClassPermissions($classes): void
    {
        foreach ($classes as $model) {
            foreach ($this->modelPermissions() as $permission) {
                foreach ($this->guardNames() as $guardName) {
                    $this->permissions[] = [
                        'name' => eval($this->config['permission_name']),
                        'guard_name' => $guardName
                    ];
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
                $models[] = Str::snake(class_basename(app('App\Filament\Resources\\' . $resourceClass)->getModel()));
            }
            return $models;
        }

        foreach ($this->config['model_directories'] as $modelDirectory) {
            $models = array_merge($models, $this->getClassesInDirectory($modelDirectory));
        }

        return $models;
    }

    private function getClassesInDirectory($path): array
    {
        $modelsPath = app_path($path);
        $files = File::files($modelsPath);
        $models = [];
        foreach ($files as $file) {
            $models[] = Str::snake($file->getFilenameWithoutExtension());
        }

        return $models;
    }

    private function modelPermissions(): array
    {
        return $this->config['model_permissions'];
    }

    private function guardNames(): array
    {
        return $this->config['guard_names'];
    }

    private function getCustomModels(): array
    {
        return $this->config['custom_models'];
    }

    private function getCustomPermissions(): array
    {
        return $this->config['custom_permissions'];
    }

    private function getExcludedModels(): array
    {
        return $this->config['excluded_models'];
    }
}
