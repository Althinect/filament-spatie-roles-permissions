<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource;

    public function __construct()
    {
        self::$resource = config('filament-spatie-roles-permissions.resources.RoleResource', RoleResource::class);
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return config('filament-spatie-roles-permissions.should_redirect_to_index.roles.after_create', false)
            ? $resource::getUrl('index')
            : parent::getRedirectUrl();
    }
}
