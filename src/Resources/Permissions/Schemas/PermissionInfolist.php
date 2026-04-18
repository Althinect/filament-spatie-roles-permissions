<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\Schemas;

use Althinect\FilamentSpatieRolesPermissions\Support\GuardName;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionSupport;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $details = [
            TextEntry::make('name')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.permission_name')),
            GuardName::infolistEntry(),
        ];

        if (PermissionSupport::groupFilteringEnabled()) {
            $details[] = TextEntry::make('group')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.group'))
                ->badge();
        }

        $details[] = TextEntry::make('created_at')
            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.created_at'))
            ->dateTime();

        return $schema
            ->components([
                Section::make(__('filament-spatie-roles-permissions::filament-spatie.resource.permission.label'))
                    ->columns(2)
                    ->schema($details),
                Section::make(__('filament-spatie-roles-permissions::filament-spatie.field.roles'))
                    ->schema([
                        RepeatableEntry::make('roles')
                            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.roles'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
                                GuardName::infolistEntry(),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}
