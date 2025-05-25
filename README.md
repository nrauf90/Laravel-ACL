# Laravel ACL

A custom Laravel ACL (Access Control List) package that automatically scans controller methods and stores permissions in the database. This package provides a simple and efficient way to manage permissions in your Laravel application.

## Features

- Automatic permission scanning from controller methods
- Database-driven permission management
- Middleware for permission checking
- Artisan command for syncing permissions
- Easy to integrate and use

## Requirements

- PHP >= 8.0
- Laravel >= 9.0

## Installation

1. Install the package via Composer:

```bash
composer require nrauf90/laravel-acl
```

2. Publish the migration:

```bash
php artisan vendor:publish --tag=larvel-acl
```

3. Run the migration:

```bash
php artisan migrate
```

## Usage

### Sync Permissions

To sync permissions from your controllers, run:

```bash
php artisan acl:sync-permissions
```

This will:
1. Scan all your controllers
2. Extract permissions from `@acl-title` and `@acl-description` annotations
3. Store them in the database

### Adding Permissions to Controllers

Add annotations to your controller methods:

```php
/**
 * @acl-title View Users
 * @acl-description Access the users list page
 */
public function index()
{
    // Your code here
}
```

### Checking Permissions

#### Using Middleware in Routes

You can use the middleware in two ways:

1. If registered in Kernel.php:
```php
Route::middleware('check.acl:view-users')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

2. Directly using the middleware class (no Kernel registration needed):
```php
use Nrauf90\LaravelAcl\Middleware\PermissionMiddleware;

Route::middleware(['web', 'auth', 'check.acl:view-users'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users', [UserController::class, 'index']);
    });

// Multiple permissions
Route::middleware(['web', 'auth', 'check.acl:view-users,edit-users'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit']);
    });

// Any permission
Route::middleware(['web', 'auth', 'check.acl:view-users|edit-users'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users/{user}', [UserController::class, 'show']);
    });
```

#### Checking in Code

```php
// Check single permission
if (auth()->user()->hasPermission('view-users')) {
    // User has permission
}

// Check multiple permissions (user must have all permissions)
if (auth()->user()->hasAllPermissions(['view-users', 'edit-users'])) {
    // User has all permissions
}

// Check any permission (user must have at least one permission)
if (auth()->user()->hasAnyPermission(['view-users', 'edit-users'])) {
    // User has at least one permission
}
```

## Database Structure

The package creates the following database tables:

### Permissions Table
- `id`: Auto-incrementing primary key
- `name`: Unique permission name (format: controllerName.methodName)
- `controller`: The controller name
- `method`: The method name
- `timestamps`: Created and updated timestamps

### Roles Table
- `id`: Auto-incrementing primary key
- `name`: Unique role name (e.g., admin, editor)
- `timestamps`: Created and updated timestamps

### Role-Permission Pivot Table
- `role_id`: Foreign key to roles table
- `permission_id`: Foreign key to permissions table
- Primary key combination of `role_id` and `permission_id`

### User-Role Pivot Table
- `user_id`: Foreign key to users table
- `role_id`: Foreign key to roles table
- Primary key combination of `user_id` and `role_id`

### User-Permission Pivot Table (Optional)
- `user_id`: Foreign key to users table
- `permission_id`: Foreign key to permissions table
- Primary key combination of `user_id` and `permission_id`

## Models

### Permission Model
```php
use Nrauf90\LaravelAcl\Models\Permission;

class Permission extends Model
{
    protected $fillable = ['name', 'controller', 'method'];
}
```

### Role Model
```php
use Nrauf90\LaravelAcl\Models\Role;

class Role extends Model
{
    protected $fillable = ['name'];
}
```

### User Model Integration
Add the following traits to your User model:

```php
use Nrauf90\LaravelAcl\Traits\HasRoles;
use Nrauf90\LaravelAcl\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasRoles, HasPermissions;
    // ... rest of your User model
}
```

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Author

- [nrauf90](https://github.com/nrauf90)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
