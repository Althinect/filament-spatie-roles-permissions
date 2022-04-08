<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources;

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages\CreatePermission;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages\EditPermission;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages\ListPermissions;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\Pages\ViewPermission;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource\RelationManager\RoleRelationManager;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionResource extends Resource
{

    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    public static function getLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.permission');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.roles_and_permissions');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.permissions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
                            TextInput::make('guard_name')
                                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name')),
                            BelongsToManyMultiSelect::make('roles')
                                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.roles'))
                                ->relationship('roles', 'name')
                        ])
                    ])
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
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name'))
                    ->searchable(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RoleRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit'   => EditPermission::route('/{record}/edit'),
            'view'   => ViewPermission::route('/{record}')
        ];
    }
}
