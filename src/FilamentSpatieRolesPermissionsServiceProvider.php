<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Althinect\FilamentSpatieRolesPermissions\Commands\Permission;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\PluginServiceProvider;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentSpatieRolesPermissionsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-spatie-roles-permissions';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-spatie-roles-permissions')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasCommand(Permission::class);
    }

    protected function getResources(): array
    {
        return [
            RoleResource::class,
            PermissionResource::class
        ];
    }
}
