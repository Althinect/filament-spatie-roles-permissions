<?php 

namespace Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages;

// use Althinect\FilamentSpatieRolesPermissions\Resource\PermissionResource;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

}