<?php

use Althinect\FilamentSpatieRolesPermissions\Support\GuardName;

it('uses the info color for guard name badges', function (): void {
    $tableColumn = GuardName::tableColumn();
    $infolistEntry = GuardName::infolistEntry();

    expect($tableColumn->isBadge())->toBeTrue()
        ->and($tableColumn->getColor('web'))->toBe('info')
        ->and($infolistEntry->isBadge())->toBeTrue()
        ->and($infolistEntry->getColor('web'))->toBe('info');
});

it('uses configured colors for guard name badges', function (): void {
    config()->set('filament-spatie-roles-permissions.guards.colors', [
        'web' => 'success',
        'api' => 'danger',
    ]);

    $tableColumn = GuardName::tableColumn();
    $infolistEntry = GuardName::infolistEntry();

    expect($tableColumn->getColor('web'))->toBe('success')
        ->and($tableColumn->getColor('api'))->toBe('danger')
        ->and($infolistEntry->getColor('web'))->toBe('success')
        ->and($infolistEntry->getColor('api'))->toBe('danger');
});

it('falls back to the info color when a guard badge color is not configured', function (): void {
    config()->set('filament-spatie-roles-permissions.guards.colors', [
        'web' => 'success',
    ]);

    $tableColumn = GuardName::tableColumn();
    $infolistEntry = GuardName::infolistEntry();

    expect($tableColumn->getColor('api'))->toBe('info')
        ->and($infolistEntry->getColor('api'))->toBe('info');
});
