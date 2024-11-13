<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Filament\Resources\Pages\EditRecord;

class EditPermission extends EditRecord
{
    protected static string $resource;

    public function __construct()
    {
        self::$resource = config('filament-spatie-roles-permissions.resources.PermissionResource', PermissionResource::class);
    }

    protected function getRedirectUrl(): ?string
    {
        $resource = static::getResource();

        return config('filament-spatie-roles-permissions.should_redirect_to_index.permissions.after_edit', false)
            ? $resource::getUrl('index')
            : parent::getRedirectUrl();
    }
}
