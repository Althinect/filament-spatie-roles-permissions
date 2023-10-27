<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\RelationManager;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class UserRelationManager extends BelongsToManyRelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static function getModelLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.users');
    }

    protected static function getPluralModelLabel(): string
    {
        return __('filament-spatie-roles-permissions::filament-spatie.section.users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(config('filament-spatie-roles-permissions.generator.user_name_column'))
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(config('filament-spatie-roles-permissions.generator.user_name_column'))
                    ->searchable()
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
            ])
            ->filters([
                //
            ]);
    }
}
