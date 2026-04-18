<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Althinect\FilamentSpatieRolesPermissions\Middleware\SyncSpatiePermissionsWithFilamentTenants;
use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentSpatieRolesPermissionsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-spatie-roles-permissions';
    }

    public function register(Panel $panel): void
    {
        TenancySupport::ensureConfigurationIsValid();

        $panel->resources(Config::resources());

        if (TenancySupport::shouldSyncTeamContext()) {
            $panel->tenantMiddleware([
                SyncSpatiePermissionsWithFilamentTenants::class,
            ], isPersistent: true);
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function boot(Panel $panel): void
    {
        TenancySupport::ensureConfigurationIsValid();
    }
}
