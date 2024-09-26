<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages\CreatePermission;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages\EditPermission;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages\ListPermissions;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages\ViewPermission;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\RelationManager\RoleRelationManager;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionResource extends Resource
{
    protected static bool $isScopedToTenant = false;

    public static function getNavigationIcon(): ?string
    {
        return  config('filament-spatie-roles-permissions.icons.permission_navigation');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-spatie-roles-permissions.should_register_on_navigation.permissions', true);
    }

    public static function getModel(): string
    {
        return config('permission.models.permission', Permission::class);
    }

    public static function getLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.permission');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-spatie-roles-permissions.navigation_section_group', 'filament-spatie-roles-permissions::filament-spatie.section.roles_and_permissions'));
    }

    public static function getNavigationSort(): ?int
    {
        return  config('filament-spatie-roles-permissions.sort.permission_navigation');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.permissions');
    }

    public static function getCluster(): ?string
    {
        return config('filament-spatie-roles-permissions.clusters.permissions', null);
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name'))
                                ->required(),
                            Select::make('guard_name')
                                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name'))
                                ->options(config('filament-spatie-roles-permissions.guard_names'))
                                ->default(config('filament-spatie-roles-permissions.default_guard_name'))
                                ->visible(fn () => config('filament-spatie-roles-permissions.should_show_guard', true))
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('roles', null))
                                ->required(),
                            Select::make('roles')
                                ->multiple()
                                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.roles'))
                                ->relationship(
                                    name: 'roles',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function(Builder $query, Get $get) {
                                        if (!empty($get('guard_name'))) {
                                            $query->where('guard_name', $get('guard_name'));
                                        }
                                        if(config('permission.teams', false) && Filament::hasTenancy()) {
                                            return $query->where(config('permission.column_names.team_foreign_key'), Filament::getTenant()->id);
                                        }
                                        return $query;
                                    }
                                )
                                ->preload(config('filament-spatie-roles-permissions.preload_roles', true)),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name'))
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->toggleable(isToggledHiddenByDefault: config('filament-spatie-roles-permissions.toggleable_guard_names.permissions.isToggledHiddenByDefault', true))
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name'))
                    ->searchable()
                    ->visible(fn () => config('filament-spatie-roles-permissions.should_show_guard', true)),
            ])
            ->filters([
                SelectFilter::make('models')
                    ->label('Models')
                    ->multiple()
                    ->options(function () {
                        $commands = new \Althinect\FilamentSpatieRolesPermissions\Commands\Permission();

                        /** @var \ReflectionClass[] */
                        $models = $commands->getAllModels();

                        $options = [];

                        foreach ($models as $model) {
                            $options[$model->getShortName()] = $model->getShortName();
                        }

                        return $options;
                    })
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['values'])) {
                            $query->where(function (Builder $query) use ($data) {
                                foreach ($data['values'] as $key => $value) {
                                    if ($value) {
                                        $query->orWhere('name', 'like', eval(config('filament-spatie-roles-permissions.model_filter_key')));
                                    }
                                }
                            });
                        }

                        return $query;
                    }),
                SelectFilter::make('guard_name')
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name'))
                    ->multiple()
                    ->options(config('filament-spatie-roles-permissions.guard_names')),
            ])->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                BulkAction::make('Attach to roles')
                    ->action(function (Collection $records, array $data): void {
                        Role::whereIn('id', $data['roles'])->each(function (Role $role) use ($records): void {
                            $records->each(fn (Permission $permission) => $role->givePermissionTo($permission));
                        });
                    })
                    ->form([
                        Select::make('roles')
                            ->multiple()
                            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.role'))
                            ->options(Role::query()->pluck('name', 'id'))
                            ->required(),
                    ])->deselectRecordsAfterCompletion(),
            ])
            ->emptyStateActions(
                config('filament-spatie-roles-permissions.should_remove_empty_state_actions.permissions') ? [] :
                    [
                        Tables\Actions\CreateAction::make()
                    ]
            );
    }

    public static function getRelations(): array
    {
        return [
            RoleRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        if (config('filament-spatie-roles-permissions.should_use_simple_modal_resource.permissions')) {
            return [
                'index' => ListPermissions::route('/'),
            ];
        }

        return [
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit' => EditPermission::route('/{record}/edit'),
            'view' => ViewPermission::route('/{record}'),
        ];
    }
}
