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

### Syncing Permissions

To scan your controllers and sync permissions to the database, run:

```bash
php artisan acl:sync-permissions
```

This command will:
- Scan all controllers in your `app/Http/Controllers` directory
- Create permissions for each public method in your controllers
- Store them in the database with the format: `controllerName.methodName`

### Using the Middleware

Add the middleware to your routes or controllers:

```php
// In routes/web.php
Route::middleware(['check.acl'])->group(function () {
    // Your protected routes here
});

// Or in your controller
public function __construct()
{
    $this->middleware('check.acl');
}
```

### Checking Permissions

The package automatically adds a `hasPermission` method to your User model. You can use it to check permissions:

```php
if (auth()->user()->hasPermission('UserController', 'index')) {
    // User has permission
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
