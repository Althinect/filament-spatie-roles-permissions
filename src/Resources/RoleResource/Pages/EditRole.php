<?php 

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\Permission\PermissionRegistrar;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * @throws BindingResolutionException
     */
    public function beforeSave()
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}