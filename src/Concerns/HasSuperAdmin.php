<?php

namespace Althinect\FilamentSpatieRolesPermissions\Concerns;

use Spatie\Permission\Traits\HasRoles;

trait HasSuperAdmin
{
    use HasRoles;

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('filament-spatie-roles-permissions.super_admin_role_name', 'Super Admin'));
    }
}
