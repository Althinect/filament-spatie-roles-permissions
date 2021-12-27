<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Filament\PluginServiceProvider;
use Illuminate\Support\ServiceProvider;

class FilamentSpatieRolesPermissionsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-spatie-roles-permissions';

    protected function getResources() :array {
        return [
            PermissionResource::class
        ];
    }
}