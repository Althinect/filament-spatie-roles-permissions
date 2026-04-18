<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\PermissionResource;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;
}
