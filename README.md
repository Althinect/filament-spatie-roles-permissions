# Filament Spatie Roles Permissions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)

`althinect/filament-spatie-roles-permissions` is a Filament 5 plugin for managing Spatie roles and permissions with:

- a modern role CRUD resource
- an enum-first permissions resource
- optional Filament tenancy integration for Spatie teams
- `althinect/enum-permission` as a first-class dependency

This v4 rewrite is intentionally a new major-version foundation. It follows the newer kick-start conventions while keeping the package configurable and translation-friendly.

## Requirements

- PHP 8.4+
- Laravel 13+
- Filament 5+
- `spatie/laravel-permission` 6.x
- `althinect/enum-permission` 1.x

## Installation

Install the package:

```bash
composer require althinect/filament-spatie-roles-permissions
```

Publish Spatie Permission's config and migrations if you have not already:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Publish this package config:

```bash
php artisan vendor:publish --tag="filament-spatie-roles-permissions-config"
```

Publish translations if you want to override labels:

```bash
php artisan vendor:publish --tag="filament-spatie-roles-permissions-translations"
```

Register the plugin on your Filament panel:

```php
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;

$panel
    ->plugin(FilamentSpatieRolesPermissionsPlugin::make());
```

Make sure your authenticatable model uses Spatie's `HasRoles` trait.

## What v4 Does

### Roles

- full CRUD
- guard-aware validation
- grouped permission assignment
- optional team selector when Spatie teams are enabled without an active Filament tenant

### Permissions

- list and view only by default
- grouped by guard and permission group when the `group` column exists
- bulk assignment to roles
- guard labels sourced from `enum-permission` when available

## Enum Permission

This package now assumes `althinect/enum-permission` is part of the intended setup.

It uses:

- `config('enum-permission.guards')` as the preferred source for guard labels
- the optional `group` column for permission grouping

If `enum-permission` guard labels are not configured, the package falls back to `filament-spatie-roles-permissions.guards.fallback`.

## Optional Tenancy

Tenancy support is Filament-tenant aware and optional.

If tenancy is disabled:

- roles and permissions behave like a normal Spatie Permission setup

If tenancy is enabled and `permission.teams` is also enabled:

- the plugin syncs the current Filament tenant into Spatie's team context
- role queries scope to the current tenant when configured
- the team selector is hidden while a Filament tenant is active

If Spatie teams are enabled but there is no active Filament tenant:

- the role resource exposes a configurable team selector

If you enable package tenancy while `permission.teams` is `false`, the plugin throws a clear configuration exception.

## Configuration

The package now uses a grouped config structure:

```php
return [
    'resources' => [
        'role' => \Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RoleResource::class,
        'permission' => \Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\PermissionResource::class,
    ],

    'navigation' => [
        'group' => 'filament-spatie-roles-permissions::filament-spatie.navigation.group',
        'labels' => [
            'role' => 'filament-spatie-roles-permissions::filament-spatie.resource.role.label',
            'roles' => 'filament-spatie-roles-permissions::filament-spatie.resource.role.plural_label',
            'permission' => 'filament-spatie-roles-permissions::filament-spatie.resource.permission.label',
            'permissions' => 'filament-spatie-roles-permissions::filament-spatie.resource.permission.plural_label',
        ],
        'icons' => [
            'role' => 'heroicon-o-shield-check',
            'permission' => 'heroicon-o-key',
        ],
        'sort' => [
            'role' => null,
            'permission' => null,
        ],
        'register' => [
            'role' => true,
            'permission' => true,
        ],
    ],

    'guards' => [
        'fallback' => [
            'web' => 'Web',
        ],
        'default' => 'web',
        'show' => true,
    ],

    'roles' => [
        'preload_permissions' => true,
        'redirect_after_create' => 'view',
        'redirect_after_edit' => 'view',
        'relation_managers' => [
            'permissions' => true,
        ],
    ],

    'permissions' => [
        'read_only' => true,
        'preload_roles' => true,
        'grouping' => [
            'enabled' => true,
            'default' => 'guard_name',
        ],
        'bulk_assignment' => true,
        'relation_managers' => [
            'roles' => false,
        ],
    ],

    'teams' => [
        'model' => null,
        'ownership_relationship' => 'teams',
        'title_attribute' => 'name',
        'foreign_key' => null,
    ],

    'tenancy' => [
        'enabled' => false,
        'scope_to_current_tenant' => true,
        'sync_team_context' => true,
        'clear_permission_cache_on_tenant_switch' => true,
    ],
];
```

## Breaking Changes From Older Versions

This rewrite intentionally removes the older generator-centric surface area.

Removed:

- `permissions:sync`
- policy stub generation
- the legacy `generator` config block
- legacy flat config keys like `scope_to_tenant` and `scope_premissions_to_tenant`
- older app-specific assumptions such as a default `App\Models\Team`

Changed:

- permissions are now enum-first and read-oriented
- roles are the main editing surface
- tenancy is optional, but when enabled it is aligned with Filament tenancy and Spatie teams

## Testing

Run the package tests with:

```bash
composer test
```

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.
