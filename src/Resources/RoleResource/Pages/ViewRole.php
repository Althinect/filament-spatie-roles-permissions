<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource;

    public function __construct()
    {
        self::$resource = config('filament-spatie-roles-permissions.resources.RoleResource', RoleResource::class);
    }

    public function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
