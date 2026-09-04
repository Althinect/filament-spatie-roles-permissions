# Filament Spatie Roles Permissions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)

`althinect/filament-spatie-roles-permissions` is a Filament 5 plugin for managing Spatie roles and permissions in your admin panel.

It is intentionally focused on the Filament UI layer:

- role management
- permission browsing
- attaching and detaching permissions from roles
- bulk assigning permissions to roles
- optional Filament tenancy integration through Spatie teams

Permission generation is no longer handled here.

That responsibility now lives in the companion package [`althinect/enum-permission`](https://github.com/althinect/enum-permission), which keeps enum generation, syncing, and optional policy generation usable **without** pulling in Filament as a dependency.

## Why there are two packages

The package split is deliberate:

- [`althinect/enum-permission`](https://github.com/althinect/enum-permission) handles permission enums, syncing, and optional policy generation.
- `althinect/filament-spatie-roles-permissions` handles the Filament resources and admin experience.

This means you can use the enum-based permission workflow in projects, packages, and services that do not use Filament at all.

`althinect/enum-permission` is an optional companion when you want enum-based permission generation and syncing; the Filament UI works without it.

## An opinionated approach to permissions

> Important: this package ecosystem takes an opinionated approach to permission management.

The core idea is that permissions should be tightly coupled to the system itself, not treated as loose strings that drift away from the codebase over time.

Using enums makes permissions easier to define, discover, refactor, and reuse. It also makes them fit naturally into Laravel's authorization layer, especially when working with policies.

In practice, that means permissions can become a first-class part of your application design instead of a parallel data structure that has to be remembered manually.

This approach is especially useful when you want permissions to be used consistently across:

- Laravel policies
- authorization checks and gates
- role and permission management UIs
- refactoring and code review workflows

It is not the only valid way to model permissions, but it is the path these packages are designed to support.

## Compatibility

- PHP 8.4+
- Laravel 13+
- Filament 5+
- `spatie/laravel-permission` 8.x
- Optional: `althinect/enum-permission` 1.x for enum generation and syncing

## What this package does

### Roles resource

The roles resource is the primary editing surface.

It provides:

- list, create, view, and edit pages for roles
- guard-aware uniqueness and validation
- permission assignment when creating a role
- permission management for existing roles via the relation manager
- attach, detach, and bulk detach permission actions on existing roles
- central team selection when Spatie teams are enabled without an active Filament tenant

### Permissions resource

The permissions resource is intentionally read-focused.

It provides:

- list and view pages for permissions
- grouping by guard name
- optional grouping by permission `group`
- bulk assignment of selected permissions to a role
- guard-aware role selection labels such as `Admin (web)`

This package does **not** create or sync permissions. It assumes your permissions already exist in the database, typically via `althinect/enum-permission`.

### Guard-aware UI

The plugin uses `enum-permission` as the preferred source for guard configuration.

That affects:

- guard labels shown in Filament
- role and permission filtering
- permission assignment validation
- guard badge presentation

If `enum-permission` is not configured yet, the plugin falls back to the local `guards.fallback` config values.

### Optional tenancy integration

If you use Spatie teams together with Filament tenancy, the plugin can:

- sync the active Filament tenant into Spatie's team context
- scope role queries to the current tenant
- show a team selector when operating outside an active tenant
- clear the permission cache when the tenant changes

If tenancy is enabled in this package while `permission.teams` is disabled, the plugin fails fast with a clear configuration exception.

Central panels require a team by default. To allow a global role with no team, enable the explicit opt-in:

```php
'teams' => [
    'allow_global_roles' => true,
],
```

## What this package no longer does

This package no longer ships permission-generation commands.

It does **not**:

- generate permission enums
- sync permissions to the database
- generate policies
- provide legacy permission sync commands

If you need those features, use [`althinect/enum-permission`](https://github.com/althinect/enum-permission).

## Installation

> Important: these docs describe the v4 rewrite, which currently requires installing the beta release explicitly.

Install the plugin:

```bash
composer require althinect/filament-spatie-roles-permissions:^4.0@beta
```

If you use enum-based permission generation and syncing, install the companion package as well:

```bash
composer require althinect/enum-permission
```

If you have not already set up Spatie Permission, publish its config and migrations first:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

If you installed `enum-permission`, publish its config to define your permission generation workflow:

```bash
php artisan vendor:publish --tag="enum-permission-config"
```

Publish this package config:

```bash
php artisan vendor:publish --tag="filament-spatie-roles-permissions-config"
```

Publish translations if you want to customize labels:

```bash
php artisan vendor:publish --tag="filament-spatie-roles-permissions-translations"
```

Run your migrations:

```bash
php artisan migrate
```

Make sure your authenticatable model uses Spatie's `HasRoles` trait:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

Register the plugin in your Filament panel:

```php
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;

$panel
    ->plugin(FilamentSpatieRolesPermissionsPlugin::make());
```

## Permission generation lives in `enum-permission`

Permission generation and syncing are provided by the optional `althinect/enum-permission` companion package.

For convenience, the most common workflow looks like this:

```bash
# Generate enums interactively or for a specific model
php artisan permission:make

# Generate enums and policies
php artisan permission:make Post --policy

# Sync enum-defined permissions to the database
php artisan permission:sync
```

After that, use this Filament plugin to:

1. create and manage roles
2. attach generated permissions to roles
3. review permissions in the Filament UI
4. bulk-assign permissions to roles from the permissions resource

For the full generation workflow, command reference, and enum setup, see the [`althinect/enum-permission` README](https://github.com/althinect/enum-permission).

## Configuration overview

The config file is organized into small, focused sections.

| Section | Purpose | Examples |
| --- | --- | --- |
| `resources` | Override resource classes if needed | `role`, `permission` |
| `navigation` | Control labels, grouping, icons, and registration | `group`, `labels`, `icons`, `register` |
| `guards` | Configure fallback labels, badge colors, and defaults | `fallback`, `colors`, `default`, `show` |
| `roles` | Tune role resource behavior | `preload_permissions`, `redirect_after_create`, `relation_managers` |
| `permissions` | Tune permission resource behavior | `grouping`, `bulk_assignment`, `preload_roles` |
| `teams` | Configure central team selection | `model`, `ownership_relationship`, `title_attribute`, `foreign_key`, `allow_global_roles` |
| `tenancy` | Configure Filament tenancy integration | `enabled`, `scope_to_current_tenant`, `sync_team_context`, `clear_permission_cache_on_tenant_switch` |

The published config file is the best source of truth for the available options:

`config/filament-spatie-roles-permissions.php`

## Upgrade notes for older versions

If you are upgrading from an older release of this package, the biggest change is the package split.

### Removed from this package

- legacy permission sync commands
- policy generation logic
- generator-focused config
- older flat config structure

### Moved to `enum-permission`

- permission enum generation
- permission syncing
- optional policy generation

### Why this matters

The old workflow mixed Filament concerns with permission generation.

The new split keeps this package focused on Filament UI while `enum-permission` stays reusable in projects that want enum-based permissions but do not use Filament.

If you previously relied on an old command from this package, switch to the `enum-permission` workflow instead.

## Testing

Run the test suite with:

```bash
composer test
```

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.
