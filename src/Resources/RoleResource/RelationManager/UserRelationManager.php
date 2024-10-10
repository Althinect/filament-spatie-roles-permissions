<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\RelationManager;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public static function getRecordTitleAttribute(): ?string
    {
        return config('filament-spatie-roles-permissions.user_name_column');
    }

    /*
     * Support changing tab title in RelationManager.
     */
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.users') ?? (string) str(static::getRelationshipName())
            ->kebab()
            ->replace('-', ' ')
            ->headline();
    }

    protected static function getModelLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.users');
    }

    protected static function getPluralModelLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.users');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(config('filament-spatie-roles-permissions.user_name_column'))
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // Support changing table heading by translations.
            ->heading(__('filament-spatie-roles-permissions::filament-spatie.section.users'))
            ->columns([
                TextColumn::make(config('filament-spatie-roles-permissions.user_name_column'))
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name'))
                    ->searchable(config('filament-spatie-roles-permissions.user_name_searchable_columns')),
            ])
            ->filters([

            ])->headerActions([
                AttachAction::make(),
            ])->actions([
                DetachAction::make(),
            ])->bulkActions([
                //
            ]);
    }
}
