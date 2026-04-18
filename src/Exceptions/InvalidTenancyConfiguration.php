<?php

namespace Althinect\FilamentSpatieRolesPermissions\Exceptions;

use RuntimeException;

class InvalidTenancyConfiguration extends RuntimeException
{
    public static function spatieTeamsRequired(): self
    {
        return new self('The filament-spatie-roles-permissions tenancy integration requires `permission.teams` to be enabled.');
    }

    public static function missingTeamModel(): self
    {
        return new self('The filament-spatie-roles-permissions package requires `filament-spatie-roles-permissions.teams.model` when Spatie teams are enabled without an active Filament tenant.');
    }
}
