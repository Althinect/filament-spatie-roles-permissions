<?php

use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

test('permission form applies the configured default section column span', function () {
    config()->set('filament-spatie-roles-permissions.layout.resources.default_section_column_span', 'full');

    $livewire = new class extends \Livewire\Component {
        use \Filament\Schemas\Concerns\InteractsWithSchemas;
    };

    $schema = PermissionResource::form(Schema::make($livewire));
    $section = $schema->getComponents()[0];

    expect($section)
        ->toBeInstanceOf(Section::class)
        ->and($section->getColumnSpan('default'))->toBe('full');
});
