<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Actions\AttachAction;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function getHeaderActions(): array
    {
        return [
            EditAction::make()
        ];
    }
}
