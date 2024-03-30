<?php

namespace Althinect\FilamentSpatieRolesPermissions;

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
            ->resources(
                config('filament-spatie-roles-permissions.resources')
            );
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
