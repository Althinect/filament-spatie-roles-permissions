<?php

namespace Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Schemas;

use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionOptions;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionSupport;
use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-spatie-roles-permissions::filament-spatie.section.role_details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.name'))
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->unique(
                                ignorable: fn ($record) => $record,
                                modifyRuleUsing: function (Unique $rule, callable $get): Unique {
                                    $rule->where('guard_name', $get('guard_name') ?: Config::defaultGuard());

                                    if (! TenancySupport::usesSpatieTeams()) {
                                        return $rule;
                                    }

                                    $teamKey = TenancySupport::shouldScopeToCurrentTenant()
                                        ? TenancySupport::currentTenantKey()
                                        : $get(TenancySupport::teamForeignKey());

                                    return $rule->where(TenancySupport::teamForeignKey(), $teamKey);
                                },
                            ),
                        Select::make('guard_name')
                            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.guard_name'))
                            ->options(Config::guardOptions())
                            ->default(Config::defaultGuard())
                            ->visible(Config::get('guards.show', true))
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, mixed $state): void {
                                $set('permissions', PermissionSupport::normalizeSelectedPermissionIdsForGuard(
                                    is_array($get('permissions')) ? $get('permissions') : [],
                                    $state,
                                ));
                            })
                            ->required(),
                        Select::make(TenancySupport::teamForeignKey())
                            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.team'))
                            ->options(fn (): array => TenancySupport::teamOptions())
                            ->searchable()
                            ->preload()
                            ->visible(fn (): bool => TenancySupport::shouldShowTeamSelector())
                            ->required(fn (): bool => TenancySupport::shouldShowTeamSelector()
                                && ! Config::get('teams.allow_global_roles', false)),
                    ]),
                Section::make(__('filament-spatie-roles-permissions::filament-spatie.section.permissions'))
                    ->visibleOn('create')
                    ->schema([
                        Select::make('permissions')
                            ->label(__('filament-spatie-roles-permissions::filament-spatie.field.permissions'))
                            ->options(fn (callable $get): array => PermissionOptions::groupedForGuard($get('guard_name') ?? Config::defaultGuard()))
                            ->multiple()
                            ->searchable()
                            ->preload(Config::get('roles.preload_permissions', true))
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
