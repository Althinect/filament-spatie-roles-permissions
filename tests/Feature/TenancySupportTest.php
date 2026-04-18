<?php

use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Role;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Team;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::query()->create(['name' => 'Admin']);
    $this->actingAs($this->user);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('shows the team selector in central team mode', function (): void {
    config()->set('permission.teams', true);
    config()->set('filament-spatie-roles-permissions.tenancy.enabled', false);

    expect(TenancySupport::shouldShowTeamSelector())->toBeTrue();
});

it('hides the team selector when a filament tenant is active', function (): void {
    config()->set('permission.teams', true);
    config()->set('filament-spatie-roles-permissions.tenancy.enabled', true);

    $team = Team::query()->create(['name' => 'North']);
    $this->user->teams()->attach($team);
    Filament::setTenant($team, isQuiet: true);

    expect(TenancySupport::shouldShowTeamSelector())->toBeFalse()
        ->and(TenancySupport::shouldScopeToCurrentTenant())->toBeTrue();
});

it('returns owned team options in central management mode', function (): void {
    config()->set('permission.teams', true);

    $north = Team::query()->create(['name' => 'North']);
    $south = Team::query()->create(['name' => 'South']);
    $this->user->teams()->attach($north);

    expect(TenancySupport::teamOptions())->toBe([
        $north->id => 'North',
    ])->not->toHaveKey($south->id);
});

it('returns tenant scoped role options when a tenant is active', function (): void {
    config()->set('permission.teams', true);
    config()->set('filament-spatie-roles-permissions.tenancy.enabled', true);

    $north = Team::query()->create(['name' => 'North']);
    $south = Team::query()->create(['name' => 'South']);
    $this->user->teams()->attach([$north->id, $south->id]);
    Filament::setTenant($north, isQuiet: true);

    $northRole = Role::query()->create(['name' => 'North Manager', 'guard_name' => 'web', 'team_id' => $north->id]);
    Role::query()->create(['name' => 'South Manager', 'guard_name' => 'web', 'team_id' => $south->id]);

    expect(TenancySupport::roleOptions())->toBe([
        $northRole->id => 'North Manager',
    ]);
});

it('can include guard names in role option labels', function (): void {
    $webRole = Role::query()->create(['name' => 'Admin', 'guard_name' => 'web']);
    $apiRole = Role::query()->create(['name' => 'Admin', 'guard_name' => 'api']);

    expect(TenancySupport::roleOptions(withGuardName: true))
        ->toHaveCount(2)
        ->toMatchArray([
            $webRole->id => 'Admin (web)',
            $apiRole->id => 'Admin (api)',
        ]);
});
