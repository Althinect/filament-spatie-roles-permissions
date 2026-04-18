<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RoleResource;
use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionSupport;
use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['guard_name'] = Config::normalizeGuardName($data['guard_name'] ?? null);

        if (TenancySupport::shouldScopeToCurrentTenant()) {
            $data[TenancySupport::teamForeignKey()] = TenancySupport::currentTenantKey();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissions(
            PermissionSupport::normalizeSelectedPermissionIdsForGuard($this->data['permissions'] ?? [], $this->record->guard_name),
        );
    }

    protected function getRedirectUrl(): string
    {
        return match (Config::get('roles.redirect_after_create', 'view')) {
            'index' => static::getResource()::getUrl('index'),
            default => static::getResource()::getUrl('view', ['record' => $this->record]),
        };
    }
}
