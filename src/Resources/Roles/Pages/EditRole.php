<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RoleResource;
use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['guard_name'] = Config::normalizeGuardName($data['guard_name'] ?? null);

        if (TenancySupport::shouldScopeToCurrentTenant()) {
            $data[TenancySupport::teamForeignKey()] = TenancySupport::currentTenantKey();
        }

        return $data;
    }

    protected function getRedirectUrl(): ?string
    {
        return match (Config::get('roles.redirect_after_edit', 'view')) {
            'index' => static::getResource()::getUrl('index'),
            default => static::getResource()::getUrl('view', ['record' => $this->record]),
        };
    }
}
