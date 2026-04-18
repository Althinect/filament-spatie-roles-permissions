<?php

use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages\CreateRole;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Pages\EditRole;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\RelationManagers\PermissionsRelationManager;
use Althinect\FilamentSpatieRolesPermissions\Resources\Roles\Schemas\RoleForm;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Permission;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Role;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Team;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\User;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\Pages\InspectableCreateRole;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\Pages\InspectableEditRole;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $user = User::query()->create(['name' => 'Admin']);
    $this->actingAs($user);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('syncs selected permissions after creating a role', function (): void {
    $permission = Permission::query()->create([
        'name' => 'users.view',
        'guard_name' => 'web',
        'group' => 'Users',
    ]);

    $page = new InspectableCreateRole;
    $page->record = Role::query()->create([
        'name' => 'Manager',
        'guard_name' => 'web',
    ]);
    $page->data = [
        'permissions' => [(string) $permission->id],
    ];

    $page->exposedAfterCreate();

    expect($page->record->fresh()->permissions()->pluck('permissions.id')->all())
        ->toBe([$permission->id]);
});

it('only syncs permissions that match the created role guard', function (): void {
    $webPermission = Permission::query()->create([
        'name' => 'users.view',
        'guard_name' => 'web',
        'group' => 'Users',
    ]);
    Permission::query()->create([
        'name' => 'users.view',
        'guard_name' => 'api',
        'group' => 'Users',
    ]);

    $page = new InspectableCreateRole;
    $page->record = Role::query()->create([
        'name' => 'Manager',
        'guard_name' => 'web',
    ]);
    $page->data = [
        'permissions' => Permission::query()->pluck('id')->map(fn (int $id): string => (string) $id)->all(),
    ];

    $page->exposedAfterCreate();

    expect($page->record->fresh()->permissions()->pluck('permissions.id')->all())
        ->toBe([$webPermission->id]);
});

it('normalizes numeric guard values before creating a role', function (): void {
    config()->set('enum-permission.guards', ['web', 'api']);

    $page = new InspectableCreateRole;

    $data = $page->exposedMutateFormDataBeforeCreate([
        'name' => 'Manager',
        'guard_name' => 0,
    ]);

    expect($data['guard_name'])->toBe('web');
});

it('writes the current tenant id into created role data when tenant scoping is enabled', function (): void {
    config()->set('permission.teams', true);
    config()->set('filament-spatie-roles-permissions.tenancy.enabled', true);

    $team = Team::query()->create(['name' => 'North']);
    User::query()->sole()->teams()->attach($team);
    Filament::setTenant($team, isQuiet: true);

    $page = new InspectableCreateRole;
    $data = $page->exposedMutateFormDataBeforeCreate([
        'name' => 'Tenant Admin',
        'guard_name' => 'web',
    ]);

    expect($data['team_id'])->toBe($team->id);
});

it('hides the permission selector on the edit role form', function (): void {
    $createSchema = RoleForm::configure(Schema::make(app(CreateRole::class))->operation('create'));
    $editSchema = RoleForm::configure(Schema::make(app(EditRole::class))->operation('edit'));

    expect($createSchema->getFlatFields())
        ->toHaveKey('permissions')
        ->and($editSchema->getFlatFields())
        ->not->toHaveKey('permissions');
});

it('does not change permissions when saving a role edit', function (): void {
    $permission = Permission::query()->create([
        'name' => 'users.create',
        'guard_name' => 'web',
        'group' => 'Users',
    ]);

    $role = Role::query()->create([
        'name' => 'Author',
        'guard_name' => 'web',
    ]);
    $role->syncPermissions([$permission]);

    $page = new InspectableEditRole;
    $page->record = $role;

    $data = $page->exposedMutateFormDataBeforeSave([
        'name' => 'Author Updated',
        'guard_name' => 'web',
    ]);

    $role->update($data);

    expect($role->fresh()->permissions()->pluck('permissions.id')->all())
        ->toBe([$permission->id]);
});

it('offers a bulk detach action in the permissions relation manager', function (): void {
    $role = Role::query()->create([
        'name' => 'Manager',
        'guard_name' => 'web',
    ]);

    /** @var PermissionsRelationManager $relationManager */
    $relationManager = app(PermissionsRelationManager::class);
    $relationManager->ownerRecord = $role;
    $relationManager->pageClass = EditRole::class;

    $table = $relationManager->table(Table::make($relationManager));

    expect($table->hasBulkAction('detach'))->toBeTrue();
});

it('uses permission names as relation manager record titles', function (): void {
    $role = Role::query()->create([
        'name' => 'Manager',
        'guard_name' => 'web',
    ]);
    $permission = Permission::query()->create([
        'name' => 'users.view',
        'guard_name' => 'web',
        'group' => 'Users',
    ]);

    /** @var PermissionsRelationManager $relationManager */
    $relationManager = app(PermissionsRelationManager::class);
    $relationManager->ownerRecord = $role;
    $relationManager->pageClass = EditRole::class;

    $table = $relationManager->table(Table::make($relationManager));

    expect($table->getRecordTitleAttribute())->toBe('name')
        ->and($table->getRecordTitle($permission))->toBe('users.view');
});
