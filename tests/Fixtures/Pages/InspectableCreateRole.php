<?php

namespace Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages\CreateRole;

class InspectableCreateRole extends CreateRole
{
    public function exposedMutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateFormDataBeforeCreate($data);
    }

    public function exposedAfterCreate(): void
    {
        $this->afterCreate();
    }
}
