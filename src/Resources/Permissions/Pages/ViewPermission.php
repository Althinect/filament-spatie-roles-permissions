<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\PermissionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPermission extends ViewRecord
{
    protected static string $resource = PermissionResource::class;
}
