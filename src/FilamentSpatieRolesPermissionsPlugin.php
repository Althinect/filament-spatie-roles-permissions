<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
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
        $panel
            ->resources([
                RoleResource::class,
                PermissionResource::class,
            ]);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
