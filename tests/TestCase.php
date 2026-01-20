<?php

namespace Tests;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FilamentSpatieRolesPermissionsServiceProvider::class,
        ];
    }
}
