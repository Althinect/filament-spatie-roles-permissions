<?php

namespace Althinect\FilamentSpatieRolesPermissions\Support;

use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class PermissionSupport
{
    protected static ?bool $hasGroupColumn = null;

    /**
     * @param  array<int, mixed>  $permissions
     * @return array<int, mixed>
     */
    public static function normalizeSelectedPermissions(array $permissions): array
    {
        return array_map(
            static fn (mixed $permission): mixed => is_string($permission) && is_numeric($permission)
                ? (int) $permission
                : $permission,
            $permissions,
        );
    }

    /**
     * @param  array<int, mixed>  $permissions
     * @return array<int, int|string>
     */
    public static function normalizeSelectedPermissionIdsForGuard(array $permissions, mixed $guardName = null): array
    {
        $permissions = array_values(array_filter(
            static::normalizeSelectedPermissions($permissions),
            static fn (mixed $permission): bool => is_int($permission) || (is_string($permission) && $permission !== ''),
        ));

        if (($guardName === null) || ($permissions === [])) {
            return $permissions;
        }

        /** @var class-string<Permission> $permissionModel */
        $permissionModel = config('permission.models.permission', Permission::class);
        $permissionKeyName = app($permissionModel)->getKeyName();
        $validPermissions = $permissionModel::query()
            ->where('guard_name', Config::normalizeGuardName($guardName))
            ->whereKey($permissions)
            ->pluck($permissionKeyName)
            ->mapWithKeys(static fn (mixed $permissionId): array => [(string) $permissionId => true])
            ->all();

        return array_values(array_filter(
            $permissions,
            static fn (int|string $permission): bool => array_key_exists((string) $permission, $validPermissions),
        ));
    }

    public static function hasGroupColumn(): bool
    {
        if (static::$hasGroupColumn !== null) {
            return static::$hasGroupColumn;
        }

        $modelClass = config('permission.models.permission', Permission::class);
        $model = app($modelClass);
        $connection = $model->getConnectionName();
        $table = $model->getTable();

        try {
            return static::$hasGroupColumn = Schema::connection($connection)->hasColumn($table, 'group');
        } catch (\Throwable) {
            return static::$hasGroupColumn = false;
        }
    }

    public static function groupFilteringEnabled(): bool
    {
        return Config::get('permissions.grouping.enabled', true) && static::hasGroupColumn();
    }

    public static function resetColumnCache(): void
    {
        static::$hasGroupColumn = null;
    }
}
