<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Tables;

use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Althinect\FilamentSpatieRolesPermissions\Support\GuardName;
use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RoleTable
{
    public static function configure(Table $table): Table
    {
        $columns = [
            TextColumn::make('name')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name'))
                ->searchable()
                ->sortable(),
            GuardName::tableColumn()
                ->searchable()
                ->sortable(),
        ];

        if (TenancySupport::usesSpatieTeams()) {
            $columns[] = TextColumn::make(TenancySupport::teamForeignKey())
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.team'))
                ->getStateUsing(fn ($record): ?string => $record->{TenancySupport::teamForeignKey()} ? (TenancySupport::teamOptions()[$record->{TenancySupport::teamForeignKey()}] ?? (string) $record->{TenancySupport::teamForeignKey()}) : null)
                ->toggleable();
        }

        $filters = [
            SelectFilter::make('guard_name')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.filter.guard_name'))
                ->options(Config::guardOptions()),
        ];

        if (TenancySupport::usesSpatieTeams() && ! TenancySupport::shouldScopeToCurrentTenant()) {
            $filters[] = SelectFilter::make(TenancySupport::teamForeignKey())
                ->label(__('filament-spatie-roles-permissions::filament-spatie.filter.team'))
                ->options(fn (): array => TenancySupport::teamOptions());
        }

        return $table
            ->columns($columns)
            ->filters($filters)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
