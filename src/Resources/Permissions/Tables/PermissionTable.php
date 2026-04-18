<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\Tables;

use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Althinect\FilamentSpatieRolesPermissions\Support\GuardName;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionSupport;
use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

class PermissionTable
{
    public static function configure(Table $table): Table
    {
        $columns = [
            TextColumn::make('name')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.permission_name'))
                ->searchable()
                ->sortable(),
            GuardName::tableColumn()
                ->searchable()
                ->sortable(),
        ];

        $filters = [
            SelectFilter::make('guard_name')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.filter.guard_name'))
                ->options(Config::guardOptions()),
        ];

        $groups = [
            Group::make('guard_name')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.filter.guard_name'))
                ->collapsible(),
        ];

        if (PermissionSupport::groupFilteringEnabled()) {
            $columns[] = TextColumn::make('group')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.group'))
                ->badge()
                ->sortable();

            $filters[] = SelectFilter::make('group')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.filter.group'))
                ->options(fn (): array => Permission::query()->whereNotNull('group')->distinct()->orderBy('group')->pluck('group', 'group')->all());

            $groups[] = Group::make('group')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.filter.group'))
                ->collapsible();
        }

        return $table
            ->columns($columns)
            ->filters($filters)
            ->groups($groups)
            ->defaultGroup(Config::get('permissions.grouping.default', 'guard_name'))
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make(array_filter([
                    Config::get('permissions.bulk_assignment', true) ? static::assignPermissionsToRoleAction() : null,
                ])),
            ]);
    }

    protected static function assignPermissionsToRoleAction(): BulkAction
    {
        return BulkAction::make('assign-permissions-to-role')
            ->label(__('filament-spatie-roles-permissions::filament-spatie.action.assign_permissions'))
            ->schema([
                Select::make('team_id')
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.team'))
                    ->options(fn (): array => TenancySupport::teamOptions())
                    ->visible(fn (): bool => TenancySupport::usesSpatieTeams() && ! TenancySupport::shouldScopeToCurrentTenant())
                    ->required(fn (): bool => TenancySupport::usesSpatieTeams() && ! TenancySupport::shouldScopeToCurrentTenant())
                    ->live(),
                Select::make('role_id')
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.role'))
                    ->options(fn (Get $get): array => TenancySupport::roleOptions(null, $get('team_id'), true))
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                $role = app(config('permission.models.role'))->newQuery()->findOrFail($data['role_id']);
                $guards = $records->pluck('guard_name')->unique()->values();

                if ($guards->count() > 1 || $guards->first() !== $role->guard_name) {
                    Notification::make()
                        ->title(__('filament-spatie-roles-permissions::filament-spatie.message.guard_mismatch'))
                        ->danger()
                        ->send();

                    return;
                }

                $role->givePermissionTo($records->all());

                Notification::make()
                    ->title(__('filament-spatie-roles-permissions::filament-spatie.message.permissions_assigned'))
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
