<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Althinect\FilamentSpatieRolesPermissions\Commands\Permission;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSpatieRolesPermissionsServiceProvider extends PackageServiceProvider
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
}
