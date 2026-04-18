<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Filament\Events\TenantSet;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Permission\PermissionRegistrar;

class FilamentSpatieRolesPermissionsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-spatie-roles-permissions';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-spatie-roles-permissions')
            ->hasConfigFile()
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('FilamentSpatieRolesPermissions', FilamentSpatieRolesPermissions::class);
    }

    public function packageBooted(): void
    {
        Event::listen(TenantSet::class, function (): void {
            if (! config('filament-spatie-roles-permissions.tenancy.enabled')) {
                return;
            }

            if (! config('filament-spatie-roles-permissions.tenancy.clear_permission_cache_on_tenant_switch', true)) {
                return;
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    }
}
