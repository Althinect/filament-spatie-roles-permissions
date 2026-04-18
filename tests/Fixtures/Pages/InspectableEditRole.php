<?php

namespace Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages\EditRole;

class InspectableEditRole extends EditRole
{
    public function exposedMutateFormDataBeforeFill(array $data): array
    {
        return $this->mutateFormDataBeforeFill($data);
    }

    public function exposedMutateFormDataBeforeSave(array $data): array
    {
        return $this->mutateFormDataBeforeSave($data);
    }

    public function exposedAfterSave(): void
    {
        $this->afterSave();
    }
}
