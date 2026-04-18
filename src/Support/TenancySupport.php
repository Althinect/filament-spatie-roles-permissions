<?php

namespace Althinect\FilamentSpatieRolesPermissions\Support;

use Althinect\FilamentSpatieRolesPermissions\Exceptions\InvalidTenancyConfiguration;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenancySupport
{
    public static function ensureConfigurationIsValid(): void
    {
        if (! Config::get('tenancy.enabled', false)) {
            return;
        }

        if (! config('permission.teams', false)) {
            throw InvalidTenancyConfiguration::spatieTeamsRequired();
        }
    }

    public static function usesSpatieTeams(): bool
    {
        return (bool) config('permission.teams', false);
    }

    public static function teamForeignKey(): string
    {
        return (string) (Config::get('teams.foreign_key') ?: config('permission.column_names.team_foreign_key', 'team_id'));
    }

    public static function shouldSyncTeamContext(): bool
    {
        return Config::get('tenancy.enabled', false)
            && Config::get('tenancy.sync_team_context', true)
            && static::usesSpatieTeams();
    }

    public static function hasActiveTenant(): bool
    {
        if (! Config::get('tenancy.enabled', false)) {
            return false;
        }

        if (! Filament::hasTenancy()) {
            return false;
        }

        return Filament::getTenant() instanceof Model;
    }

    public static function currentTenant(): ?Model
    {
        $tenant = Filament::getTenant();

        return $tenant instanceof Model ? $tenant : null;
    }

    public static function currentTenantKey(): int|string|null
    {
        return static::currentTenant()?->getKey();
    }

    public static function shouldScopeToCurrentTenant(): bool
    {
        return Config::get('tenancy.enabled', false)
            && Config::get('tenancy.scope_to_current_tenant', true)
            && static::usesSpatieTeams()
            && static::hasActiveTenant();
    }

    public static function shouldShowTeamSelector(): bool
    {
        return static::usesSpatieTeams() && ! static::shouldScopeToCurrentTenant();
    }

    public static function syncCurrentTenantTeamContext(): void
    {
        if (! static::shouldSyncTeamContext() || ! static::hasActiveTenant()) {
            return;
        }

        $tenantKey = static::currentTenantKey();

        if ($tenantKey === null) {
            return;
        }

        setPermissionsTeamId($tenantKey);
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenantKey);
    }

    public static function scopeRoleQuery(Builder $query): Builder
    {
        if (! static::shouldScopeToCurrentTenant()) {
            return $query;
        }

        return $query->where(static::teamForeignKey(), static::currentTenantKey());
    }

    /**
     * @return array<int|string, string>
     */
    public static function teamOptions(): array
    {
        $teamModel = Config::get('teams.model');

        if (! is_string($teamModel) || $teamModel === '') {
            throw InvalidTenancyConfiguration::missingTeamModel();
        }

        /** @var Model&Builder $teamQuery */
        $teamQuery = $teamModel::query();

        $user = auth()->user();
        $relationship = Config::get('teams.ownership_relationship');

        if ($user instanceof Authenticatable && is_string($relationship) && method_exists($user, $relationship)) {
            $ownedTeams = $user->{$relationship}();

            if ($ownedTeams instanceof Relation || $ownedTeams instanceof Builder) {
                return $ownedTeams
                    ->pluck(Config::get('teams.title_attribute', 'name'), $teamQuery->getModel()->getKeyName())
                    ->all();
            }
        }

        return $teamQuery
            ->pluck(Config::get('teams.title_attribute', 'name'), $teamQuery->getModel()->getKeyName())
            ->all();
    }

    /**
     * @return array<int|string, string>
     */
    public static function roleOptions(mixed $guardName = null, int|string|null $teamId = null, bool $withGuardName = false): array
    {
        /** @var class-string<Role> $roleModel */
        $roleModel = config('permission.models.role', Role::class);
        $query = $roleModel::query()->orderBy('name')->orderBy('guard_name');

        if ($guardName !== null) {
            $query->where('guard_name', Config::normalizeGuardName($guardName));
        }

        if (static::shouldScopeToCurrentTenant()) {
            $query->where(static::teamForeignKey(), static::currentTenantKey());
        } elseif (static::usesSpatieTeams() && $teamId !== null) {
            $query->where(static::teamForeignKey(), $teamId);
        }

        if (! $withGuardName) {
            return $query->pluck('name', 'id')->all();
        }

        return $query
            ->get([$query->getModel()->getKeyName(), 'name', 'guard_name'])
            ->mapWithKeys(static fn (Role $role): array => [
                $role->getKey() => "{$role->name} ({$role->guard_name})",
            ])
            ->all();
    }
}
