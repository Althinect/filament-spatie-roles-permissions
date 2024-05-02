<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): string
    {
        return config('filament-spatie-roles-permissions.should_redirect_to_index.roles.after_create')
            ? RoleResource::getUrl('index')
            : RoleResource::getUrl('view');
    }
}
