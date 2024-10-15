# Description

[![Latest Version on Packagist](https://img.shields.io/packagist/v/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/althinect/filament-spatie-roles-permissions.svg?style=flat-square)](https://packagist.org/packages/althinect/filament-spatie-roles-permissions)
![GitHub Actions](https://github.com/althinect/filament-spatie-roles-permissions/actions/workflows/main.yml/badge.svg)

This plugin is built on top of [Spatie's Permission](https://spatie.be/docs/laravel-permission/v6/introduction) package. 

Provides Resources for Roles and Permissions

Permission and Policy generations
- Check the ``config/filament-spatie-roles-permissions-config.php``

Supports permissions for teams
- Make sure the ``teams`` attribute in the ``config/permission.php`` file is set to ``true``

## Updating

After performing a ```composer update```, run

```php
php artisan vendor:publish --tag="filament-spatie-roles-permissions-config" --force
```
***Note that your existing settings will be overriden***

#### If you like our work Don't forget to STAR the project 

## Installation

You can install the package via composer:

```bash
composer require althinect/filament-spatie-roles-permissions
```

Since the package depends on [Spatie's Permission](https://spatie.be/docs/laravel-permission/v5/introduction) package. You have to publish the migrations by running:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Add the plugin to the `AdminPanelProvider`
```php
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;

$panel
    ...
    ->plugin(FilamentSpatieRolesPermissionsPlugin::make())

```

Now you should add any other configurations needed for the Spatie-Permission package.

**Note:** This will override your existing config file.
You can publish the config file of the package with:
```bash
php artisan vendor:publish --tag="filament-spatie-roles-permissions-config" --force
```

You can publish translations with:

```bash
php artisan vendor:publish --tag="filament-spatie-roles-permissions-translations"
```

Don't forget to add the `HasRoles` trait to your User model.

```php
 // The User model requires this trait
 use HasRoles;
 ```

## Usage

### Form

You can add the following to your *form* method in your UserResource 

```php
return $form->schema([
    Select::make('roles')->multiple()->relationship('roles', 'name')
])
```

In addition to the field added to the **UserResource**. There will be 2 Resources published under *Roles and Permissions*. You can use these resources manage roles and permissions.

### Generate Permissions

You can generate Permissions by running
```bash
php artisan permissions:sync
```

This will not delete any existing permissions. However, if you want to delete all existing permissions, run

```bash
php artisan permissions:sync -C|--clean
```

There may be an occassion where you wish to hard reset and truncate your existing permissions. To delete all permissions and reset the primary key, run

```bash
php artisan permissions:sync -H|--hard
```

#### Example: 
If you have a **Post** model, it will generate the following permissions
```
view-any Post
view Post
create Post
update Post
delete Post
restore Post
force-delete Post
replicate Post
reorder Post
```

### Generating Policies
To generate policies use the command below. This won't replace any existing policies

```bash
php artisan permissions:sync -P|--policies
```

### Overriding existing Policies
This will override existing policy classes

```bash
php artisan permissions:sync -O|--oep
```

### Role and Permission Policies

Create a RolePolicy and PermissionPolicy if you wish to control the visibility of the resources on the navigation menu.
Make sure to add them to the AuthServiceProvider.
> **ℹ️ Info:** *Laravel 11 removed `AuthServiceProvider`, so, in this case, we need to use `AppServiceProvider` instead.*

```bash
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

Gate::policy(Role::class, RolePolicy::class);
Gate::policy(Permission::class, PermissionPolicy::class);
```

### Ignoring prompts
You can ignore any prompts by add the flag ``-Y`` or ``--yes-to-all`` 

***Recommended only for new projects as it will replace Policy files***

```bash
php artisan permissions:sync -COPY
```

### Adding a Super Admin

* Create a Role with the name `Super Admin` and assign the role to a User
* Add the following trait to the User Model

```php
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;

class User extends Authenticatable{

...
use HasSuperAdmin;
```

* In the `boot` method of the `AuthServiceProvider` add the following

```php
Gate::before(function (User $user, string $ability) {
    return $user->isSuperAdmin() ? true: null;     
});
```

### Guard Names
When you use any guard other than `web` you have to add the guard name to the `config/auth.php` file.
Example: If you use `api` guard, you should add the following to the `guards` array

```php

'guards' => [
    ...

    'api' => [
        'driver' => 'token',
        'provider' => 'users',
        'hash' => false,
    ],
],
```

### Tenancy

- Make sure to set the following on the `config/permission.php`
```php
'teams' => true
```

- Make sure the `team_model` on the `config/permission` is correctly set.
- Create a Role model which extends `Spatie\Permission\Models\Role`
- Replace the model in the `config/permission.php` with the newly created models
- Add the `team` relationship in both models

```php
...
public function team(): BelongsTo
{
    return $this->belongsTo(Team::class);
}
```
- Add the following to the `AdminPanelProvider` to support tenancy


```php
use Althinect\FilamentSpatieRolesPermissions\Middleware\SyncSpatiePermissionsWithFilamentTenants;

$panel
    ...
    ->tenantMiddleware([
        SyncSpatiePermissionsWithFilamentTenants::class,
    ], isPersistent: true)
```

- Use the following within you UserResource

```
Forms\Components\Select::make('roles')
            ->relationship(name: 'roles', titleAttribute: 'name')
            ->saveRelationshipsUsing(function (Model $record, $state) {
                 $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
            })
           ->multiple()
           ->preload()
           ->searchable(),
```

Follow the instructions on [Filament Multi-tenancy](https://filamentphp.com/docs/3.x/panels/tenancy)

### Configurations

In the **filament-spatie-roles-permissions.php** config file, you can customize the permission generation

## Security

If you discover any security related issues, please create an issue.

## Credits

-   [Tharinda Rodrigo](https://github.com/tharindarodrigo/)
-   [Udam Liyanage](https://github.com/UdamLiyanage/)
-   [Contributors](https://github.com/Althinect/filament-spatie-roles-permissions/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
