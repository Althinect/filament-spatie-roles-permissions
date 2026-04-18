<?php

namespace Tests;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsServiceProvider;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionSupport;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Permission;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Role;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Team;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\User;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Providers\TestPanelProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\Facades\Filament;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $enablesPackageDiscoveries = true;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $panel = Filament::getPanel('admin') ?? Filament::getDefaultPanel();

        if ($panel) {
            Filament::setCurrentPanel($panel);
        }

        Filament::setTenant(null, isQuiet: true);
        PermissionSupport::resetColumnCache();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentServiceProvider::class,
            FilamentSpatieRolesPermissionsServiceProvider::class,
            TestPanelProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        /** @var Repository $config */
        $config = $app['config'];

        $config->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $config->set('database.default', 'testing');
        $config->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $config->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);

        $config->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);

        $config->set('permission.models.role', Role::class);
        $config->set('permission.models.permission', Permission::class);
        $config->set('permission.table_names', [
            'roles' => 'roles',
            'permissions' => 'permissions',
            'model_has_permissions' => 'model_has_permissions',
            'model_has_roles' => 'model_has_roles',
            'role_has_permissions' => 'role_has_permissions',
        ]);
        $config->set('permission.column_names', [
            'role_pivot_key' => null,
            'permission_pivot_key' => null,
            'model_morph_key' => 'model_id',
            'team_foreign_key' => 'team_id',
        ]);
        $config->set('permission.teams', false);
        $config->set('permission.register_permission_check_method', true);
        $config->set('permission.cache.store', 'array');
        $config->set('permission.cache.key', 'spatie.permission.cache');

        $config->set('filament-spatie-roles-permissions.guards.fallback', [
            'web' => 'Web',
            'api' => 'API',
        ]);
        $config->set('filament-spatie-roles-permissions.guards.default', 'web');
        $config->set('filament-spatie-roles-permissions.teams.model', Team::class);
        $config->set('filament-spatie-roles-permissions.teams.ownership_relationship', 'teams');
        $config->set('filament-spatie-roles-permissions.teams.title_attribute', 'name');
        $config->set('filament-spatie-roles-permissions.permissions.grouping.enabled', true);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/database/migrations');
    }
}
