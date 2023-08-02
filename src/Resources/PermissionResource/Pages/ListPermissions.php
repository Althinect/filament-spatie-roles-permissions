<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        $roleModel = config('permission.models.role');

        return [
            BulkAction::make('Attach Role')
                ->action(function (Collection $records, array $data): void {
                    foreach ($records as $record) {
                        $record->roles()->sync($data['role']);
                        $record->save();
                    }
                })
                ->form([
                    Select::make('role')
                        ->label(__('filament-spatie-roles-permissions::filament-spatie.field.role'))
                        ->options($roleModel::query()->pluck('name', 'id'))
                        ->required(),
                ])->deselectRecordsAfterCompletion(),
        ];

    }
}
