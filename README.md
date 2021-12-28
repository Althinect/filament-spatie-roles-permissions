# Description

[![Latest Version on Packagist](https://img.shields.io/packagist/v/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)
![GitHub Actions](https://github.com/althinect/filament-spatie-roles-permissions/actions/workflows/main.yml/badge.svg)

This plugin is built on top of [Spatie's Permission](https://spatie.be/docs/laravel-permission/v5/introduction) package. 

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

## Usage

You can add this to your *form* method in your UserResource 

```php
return $form->schema([
    ...
    BelongsToManyMultiSelect::make('roles')->relationship('roles', 'name')
])

```

In addition to the field added to the UserResource. There will be 2 Resources published under *Roles and Permissions*. You can use these resources manage roles and permissions.

### Security

If you discover any security related issues, please create an issue.

## Credits

-   [Tharinda Rodrigo](https://github.com/UdamLiyanage/)
-   [Udam Liyanage](https://github.com/UdamLiyanage/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
