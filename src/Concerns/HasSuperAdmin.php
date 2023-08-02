<?php

namespace Althinect\FilamentSpatieRolesPermissions\Concerns;

use Spatie\Permission\Traits\HasRoles;

trait HasSuperAdmin
{
    use HasRoles;

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }
}
