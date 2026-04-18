<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RelationManagers;

use Althinect\FilamentSpatieRolesPermissions\Support\GuardName;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionSupport;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    public function table(Table $table): Table
    {
        $columns = [
            TextColumn::make('name')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.permission_name'))
                ->searchable()
                ->sortable(),
            GuardName::tableColumn()
                ->sortable(),
        ];

        if (PermissionSupport::groupFilteringEnabled()) {
            $columns[] = TextColumn::make('group')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.group'))
                ->badge();
        }

        return $table
            ->recordTitleAttribute('name')
            ->columns($columns)
            ->headerActions([
                AttachAction::make()
                    ->multiple()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'group'])
                    ->recordSelectOptionsQuery(fn ($query) => $query->where('guard_name', $this->ownerRecord->guard_name)),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->groupedBulkActions([
                DetachBulkAction::make(),
            ]);
    }
}
