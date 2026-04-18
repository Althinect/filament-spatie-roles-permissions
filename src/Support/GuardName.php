<?php

namespace Althinect\FilamentSpatieRolesPermissions\Support;

use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;

class GuardName
{
    public static function infolistEntry(): TextEntry
    {
        return TextEntry::make('guard_name')
            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name'))
            ->badge()
            ->color(fn (mixed $state): string => Config::guardBadgeColor($state));
    }

    public static function tableColumn(): TextColumn
    {
        return TextColumn::make('guard_name')
            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name'))
            ->badge()
            ->color(fn (mixed $state): string => Config::guardBadgeColor($state));
    }
}
