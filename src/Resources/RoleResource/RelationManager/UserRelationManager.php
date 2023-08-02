<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\RelationManager;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserRelationManager extends RelationManager
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
            ])
            ->filters([

            ])->headerActions([
                AttachAction::make()
            ])->actions([
                DetachAction::make()
            ])->bulkActions([
                //
            ]);
    }
}
