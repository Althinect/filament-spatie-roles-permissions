<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Roles;

use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages\CreateRole;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages\EditRole;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages\ListRoles;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages\ViewRole;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RelationManagers\PermissionsRelationManager;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Schemas\RoleForm;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Schemas\RoleInfolist;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Tables\RoleTable;
use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|\BackedEnum|null $navigationIcon = null;

    public static function getModel(): string
    {
        /** @var class-string<Role> */
        return config('permission.models.role', Role::class);
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Config::get('navigation.icons.role');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) Config::get('navigation.register.role', true);
    }

    public static function getNavigationSort(): ?int
    {
        return Config::get('navigation.sort.role');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(Config::get('navigation.group'));
    }

    public static function getLabel(): string
    {
        return __(Config::get('navigation.labels.role'));
    }

    public static function getPluralLabel(): string
    {
        return __(Config::get('navigation.labels.roles'));
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoleTable::configure($table);
    }

    public static function getRelations(): array
    {
        $relations = [];

        if (Config::get('roles.relation_managers.permissions', true)) {
            $relations[] = PermissionsRelationManager::make();
        }

        return $relations;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        TenancySupport::ensureConfigurationIsValid();
        TenancySupport::syncCurrentTenantTeamContext();

        return TenancySupport::scopeRoleQuery(parent::getEloquentQuery());
    }
}
