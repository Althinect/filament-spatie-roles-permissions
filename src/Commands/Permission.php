<?php

namespace Althinect\FilamentSpatieRolesPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission as PermissionModel;
use Illuminate\Support\Facades\DB;


class Permission extends Command
{
    public array $permissions = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync {--C|clean}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates permissions through Models or Filament Resources and custom permissions';

    /**
     * Execute the console command.
     */
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
            DB::table(config('permission.table_names.permissions'))->delete();
            $this->comment('Deleted Permissions');
        }
    }

    public function prepareClassPermissions($classes): void
    {
        foreach ($classes as $class) {
            foreach ($this->modelPermissions() as $modelPermission) {
                foreach ($this->guardNames() as $guardName) {
                    $this->permissions[] = [
                        'name' => $class . '.' . $modelPermission,
                        'guard_name' => $guardName
                    ];
                }
            }
        }
    }

    public function prepareCustomPermissions()
    {
        foreach ($this->getCustomPermissions() as $customPermission) {
            foreach($this->guardNames() as $guardName){
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

        if (config('filament-spatie-roles-permissions.discover_models_through_filament_resources')) {
            $resources = File::files(app_path('../app/Filament/Resources'));

            foreach ($resources as $resource) {
                $resourceClass = $resource->getFilenameWithoutExtension();
                $models[] = Str::snake(class_basename(app('App\Filament\Resources\\' . $resourceClass)->getModel()));
            }
            return $models;
        }

        foreach (config('filament-spatie-roles-permissions.model_directories') as $modelDirectory) {
            $models = array_merge($models, $this->getClassesInDirectory($modelDirectory));
        }

        return $models;
    }

    private function getClassesInDirectory($path)
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
        return config('filament-spatie-roles-permissions.model_permissions') ?? [];
    }

    private function guardNames(): array
    {
        return config('filament-spatie-roles-permissions.guard_names') ?? [];
    }

    private function getCustomModels(): array
    {
        return config('filament-spatie-roles-permissions.custom_models') ?? [];
    }

    private function getCustomPermissions(): array
    {
        return config('filament-spatie-roles-permissions.custom_permissions') ?? [];
    }

    private function getExcludedModels(): array
    {
        return config('filament-spatie-roles-permissions.excluded_models') ?? [];
    }
}
