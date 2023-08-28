<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\RelationManager;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\Permission\PermissionRegistrar;

class PermissionRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'permissions';

    protected static ?string $recordTitleAttribute = 'name';

    protected static function getModelLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.permission');
    }

    protected static function getPluralModelLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.permissions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
                TextInput::make('guard_name')
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name'))

            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
                TextColumn::make('guard_name')
                    ->searchable()
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name')),

            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->after(fn() => app()->make(PermissionRegistrar::class)->forgetCachedPermissions()),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()->after(fn() => app()->make(PermissionRegistrar::class)->forgetCachedPermissions()),
            ])
            ->filters([
                //
            ]);
    }

    /**
     * @throws BindingResolutionException
     */
    public function afterAttach()
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @throws BindingResolutionException
     */
    public function afterBulkDelete()
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @throws BindingResolutionException
     */
    public function afterBulkDetach()
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
