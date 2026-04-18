<?php

namespace Althinect\FilamentSpatieRolesPermissions\Support;

use Spatie\Permission\Models\Permission;

class PermissionOptions
{
    /**
     * @return array<int|string, string|array<int|string, string>>
     */
    public static function groupedForGuard(mixed $guardName = null): array
    {
        /** @var class-string<Permission> $permissionModel */
        $permissionModel = config('permission.models.permission', Permission::class);
        $query = $permissionModel::query()->orderBy('name');

        if ($guardName !== null) {
            $query->where('guard_name', Config::normalizeGuardName($guardName));
        }

        if (! PermissionSupport::groupFilteringEnabled()) {
            return $query->pluck('name', 'id')->all();
        }

        $query->orderBy('group')->orderBy('name');

        return $query
            ->get(['id', 'name', 'group'])
            ->groupBy(fn ($permission): string => $permission->group ?: __('filament-spatie-roles-permissions::filament-spatie.group.ungrouped'))
            ->map(fn ($permissions): array => $permissions->pluck('name', 'id')->all())
            ->all();
    }
}
