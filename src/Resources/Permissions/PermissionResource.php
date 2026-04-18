<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Permissions;

use Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\Pages\ListPermissions;
use Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\Pages\ViewPermission;
use Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\Schemas\PermissionInfolist;
use Althinect\FilamentSpatieRolesPermissions\Resources\Permissions\Tables\PermissionTable;
use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|\BackedEnum|null $navigationIcon = null;

    public static function getModel(): string
    {
        /** @var class-string<Permission> */
        return config('permission.models.permission', Permission::class);
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Config::get('navigation.icons.permission');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) Config::get('navigation.register.permission', true);
    }

    public static function getNavigationSort(): ?int
    {
        return Config::get('navigation.sort.permission');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(Config::get('navigation.group'));
    }

    public static function getLabel(): string
    {
        return __(Config::get('navigation.labels.permission'));
    }

    public static function getPluralLabel(): string
    {
        return __(Config::get('navigation.labels.permissions'));
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
            'view' => ViewPermission::route('/{record}'),
        ];
    }
}
