<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
