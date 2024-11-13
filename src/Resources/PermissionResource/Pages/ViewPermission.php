<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermission extends ViewRecord
{
    protected static string $resource;

    public function __construct()
    {
        self::$resource = config('filament-spatie-roles-permissions.resources.PermissionResource', PermissionResource::class);
    }

    public function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
