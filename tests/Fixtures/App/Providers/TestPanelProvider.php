<?php

namespace Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Providers;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Althinect\FilamentSpatieRolesPermissions\Tests\Fixtures\App\Models\Team;
use Filament\Panel;
use Filament\PanelProvider;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->tenant(Team::class, ownershipRelationship: 'teams')
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make());
    }
}
