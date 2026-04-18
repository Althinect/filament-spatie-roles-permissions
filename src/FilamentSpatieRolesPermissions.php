<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Althinect\FilamentSpatieRolesPermissions\Support\Config;

class FilamentSpatieRolesPermissions
{
    /**
     * @return array<class-string>
     */
    public function resources(): array
    {
        return Config::resources();
    }
}
