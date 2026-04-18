<?php

use Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RoleResource;

return [

    'resources' => [
        'role' => RoleResource::class,
        'permission' => PermissionResource::class,
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
        'colors' => [
            'web' => 'info',
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
