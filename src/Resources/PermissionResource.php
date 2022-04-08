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

    protected static ?string $navigationGroup = 'Roles and Permissions';

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name'),
                            TextInput::make('guard_name'),
                            BelongsToManyMultiSelect::make('roles')
                                ->relationship('roles', 'name')
                        ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('guard_name')->searchable(),

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
