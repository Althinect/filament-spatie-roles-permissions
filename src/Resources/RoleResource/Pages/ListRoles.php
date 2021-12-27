<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
