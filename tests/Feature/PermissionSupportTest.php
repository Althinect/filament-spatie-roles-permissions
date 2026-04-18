<?php

use Althinect\FilamentSpatieRolesPermissions\Support\Config;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionOptions;
use Althinect\FilamentSpatieRolesPermissions\Support\PermissionSupport;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('prefers enum-permission guard labels when available', function (): void {
    config()->set('enum-permission.guards', [
        'web' => 'Web',
        'api' => 'API',
    ]);

    expect(Config::guardOptions())->toBe([
        'web' => 'Web',
        'api' => 'API',
    ]);
});

it('normalizes enum-permission guard lists into select-friendly options', function (): void {
    config()->set('enum-permission.guards', ['web', 'api']);

    expect(Config::guardOptions())->toBe([
        'web' => 'Web',
        'api' => 'API',
    ])->and(Config::normalizeGuardName(0))->toBe('web')
        ->and(Config::normalizeGuardName('1'))->toBe('api');
});

it('groups permissions by permission group for the selected guard', function (): void {
    Permission::query()->create(['name' => 'users.view', 'guard_name' => 'web', 'group' => 'Users']);
    Permission::query()->create(['name' => 'users.update', 'guard_name' => 'web', 'group' => 'Users']);
    Permission::query()->create(['name' => 'roles.view', 'guard_name' => 'api', 'group' => 'Roles']);

    expect(PermissionOptions::groupedForGuard('web'))->toBe([
        'Users' => [
            2 => 'users.update',
            1 => 'users.view',
        ],
    ]);
});

it('keeps only selected permission ids that match the chosen guard', function (): void {
    $webPermission = Permission::query()->create(['name' => 'users.view', 'guard_name' => 'web', 'group' => 'Users']);
    $apiPermission = Permission::query()->create(['name' => 'users.view', 'guard_name' => 'api', 'group' => 'Users']);

    expect(PermissionSupport::normalizeSelectedPermissionIdsForGuard([
        (string) $webPermission->id,
        (string) $apiPermission->id,
    ], 'web'))->toBe([$webPermission->id]);
});

it('gracefully disables grouping when the group column is missing', function (): void {
    Schema::table('permissions', function ($table): void {
        $table->dropColumn('group');
    });

    PermissionSupport::resetColumnCache();
    Permission::query()->create(['name' => 'users.view', 'guard_name' => 'web']);

    expect(PermissionSupport::groupFilteringEnabled())->toBeFalse()
        ->and(PermissionOptions::groupedForGuard('web'))->toBe([
            1 => 'users.view',
        ]);
});
