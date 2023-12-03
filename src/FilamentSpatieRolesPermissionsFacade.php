<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Illuminate\Support\Facades\Facade;

class FilamentSpatieRolesPermissionsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'filament-spatie-roles-permissions';
    }
}
