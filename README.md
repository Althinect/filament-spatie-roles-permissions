# Description

[![Latest Version on Packagist](https://img.shields.io/packagist/v/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)
![GitHub Actions](https://github.com/althinect/filament-spatie-roles-permissions/actions/workflows/main.yml/badge.svg)

This plugin is built on top of [Spatie's Permission](https://spatie.be/docs/laravel-permission/v5/introduction) package. 

## Updating

After performing a ```composer update```, run
```php
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

```php
php artisan vendor:publish --tag="filament-spatie-roles-permissions-config"
```

## Installation

You can install the package via composer:

```bash
composer require althinect/filament-spatie-roles-permissions
```

Since the package depends on [Spatie's Permission](https://spatie.be/docs/laravel-permission/v5/introduction) package. You have to publish the migrations by running:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Now you should add any other configurations needed for the Spatie-Permission package.

You can publish the config file of the package with:
```bash
php artisan vendor:publish --tag="filament-spatie-roles-permissions-config"
```

## Usage

### Form

You can add the following to your *form* method in your UserResource 

```php
return $form->schema([
    Select::make('roles')->multipe()->relationship('roles', 'name')
])
```

In addition to the field added to the **UserResource**. There will be 2 Resources published under *Roles and Permissions*. You can use these resources manage roles and permissions.

### Generate Permissions

You can generate Permissions by running
```bash
php artisan permission:sync
```

This will not delete any existing permissions. However, if you want to delete all existing permissions, run

```bash
php artisan permission:sync --clean
```

#### Example: 
If you have a **Post** model, it will generate the following permissions
```
post.view-any
post.view
post.create
post.update
post.delete
post.restore
post.force-delete
```

### Configurations

In the **filament-spatie-roles-permissions.php** config file, you can modify the following

```php
'generator' => [

        'guard_names' => [
            'web',
            //'api'
        ],

        'model_permissions' => [
            'view-any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force-delete'
        ],

        /*
         * Permissions will be generated only for the models associated with the respective Filament Resources
         */
        'discover_models_through_filament_resources' => true,

        /*
         * If you have custom model directories, include them here.
         */
        'model_directories' => [
            'Models',
            //'Domains/Posts/Models'
        ],

        /*
         * Define custom_models in snake-case
         */
        'custom_models' => [
            //'roles',
            //'permissions'
        ],

        /*
         * Define excluded_models in snake-case
         */
        'excluded_models' => [
            'team',
        ],

        /*
         * Define any other permission here
         */
        'custom_permissions' => [
            //'log.view'
        ]
    ]
```

## Security

If you discover any security related issues, please create an issue.

## Credits

-   [Tharinda Rodrigo](https://github.com/UdamLiyanage/)
-   [Udam Liyanage](https://github.com/UdamLiyanage/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
