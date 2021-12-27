<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\PluginServiceProvider;
use Illuminate\Support\ServiceProvider;

class FilamentSpatieRolesPermissionsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-spatie-roles-permissions';

    protected function getResources() :array {
        return [
            RoleResource::class,
            PermissionResource::class
        ];
    }
}