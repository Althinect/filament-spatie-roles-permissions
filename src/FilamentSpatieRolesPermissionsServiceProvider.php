<?php

namespace Althinect\FilamentSpatieRolesPermissions;

use Illuminate\Support\ServiceProvider;

class FilamentSpatieRolesPermissionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament-spatie-roles-permissions');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-spatie-roles-permissions');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('filament-spatie-roles-permissions.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/filament-spatie-roles-permissions'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/filament-spatie-roles-permissions'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/filament-spatie-roles-permissions'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'filament-spatie-roles-permissions');

        // Register the main class to use with the facade
        $this->app->singleton('filament-spatie-roles-permissions', function () {
            return new FilamentSpatieRolesPermissions;
        });
    }
}
