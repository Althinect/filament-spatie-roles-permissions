<?php

use Althinect\FilamentSpatieRolesPermissions\Exceptions\InvalidTenancyConfiguration;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Althinect\FilamentSpatieRolesPermissions\Middleware\SyncSpatiePermissionsWithFilamentTenants;
use Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RoleResource;
use Filament\Panel;

it('registers the rewritten resources on the plugin', function (): void {
    $panel = Panel::make()->id('test');

    FilamentSpatieRolesPermissionsPlugin::make()->register($panel);

    expect($panel->getResources())
        ->toContain(RoleResource::class)
        ->toContain(PermissionResource::class);
});

it('adds persistent tenant middleware when tenancy sync is enabled', function (): void {
    config()->set('permission.teams', true);
    config()->set('filament-spatie-roles-permissions.tenancy.enabled', true);
    config()->set('filament-spatie-roles-permissions.tenancy.sync_team_context', true);

    $panel = Panel::make()->id('tenant-test');

    FilamentSpatieRolesPermissionsPlugin::make()->register($panel);

    expect($panel->getTenantMiddleware())
        ->toContain(SyncSpatiePermissionsWithFilamentTenants::class);
});

it('fails fast when package tenancy is enabled without spatie teams', function (): void {
    config()->set('permission.teams', false);
    config()->set('filament-spatie-roles-permissions.tenancy.enabled', true);

    $panel = Panel::make()->id('invalid-tenant-config');

    expect(fn () => FilamentSpatieRolesPermissionsPlugin::make()->register($panel))
        ->toThrow(InvalidTenancyConfiguration::class);
});
