<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Schemas;

use Althinect\FilamentSpatieRolesPermissions\Support\GuardName;
use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $components = [
            TextEntry::make('name')
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name')),
            GuardName::infolistEntry(),
        ];

        if (TenancySupport::usesSpatieTeams()) {
            $components[] = TextEntry::make(TenancySupport::teamForeignKey())
                ->label(__('filament-spatie-roles-permissions::filament-spatie.field.team'))
                ->getStateUsing(fn ($record): ?string => $record->{TenancySupport::teamForeignKey()} ? (TenancySupport::teamOptions()[$record->{TenancySupport::teamForeignKey()}] ?? (string) $record->{TenancySupport::teamForeignKey()}) : null);
        }

        $components[] = TextEntry::make('created_at')
            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.created_at'))
            ->dateTime();

        return $schema
            ->components([
                Section::make(__('filament-spatie-roles-permissions::filament-spatie.section.role_details'))
                    ->columns(2)                
                    ->schema($components),
                // Section::make(__('filament-spatie-roles-permissions::filament-spatie.section.permissions'))
                //     ->schema([
                //         RepeatableEntry::make('permissions')
                //             ->label(__('filament-spatie-roles-permissions::filament-spatie.field.permissions'))
                //             ->schema([
                //                 TextEntry::make('name')
                //                     ->label(__('filament-spatie-roles-permissions::filament-spatie.field.permission_name')),
                //                 GuardName::infolistEntry(),
                //             ])
                //             ->columns(2),
                //     ]),
            ]);
    }
}
