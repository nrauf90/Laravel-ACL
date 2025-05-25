# Laravel ACL Package

A custom Laravel ACL (Access Control List) package to auto-scan public controller methods and register them as permissions in the database. Includes middleware for dynamic access control.

---

## Features

- Automatically scans public controller methods.
- Stores permissions in the `permissions` table.
- Middleware to check if the authenticated user has access.
- Ready for role/permission expansion.

---

## Installation

### 1. Add the package to your Laravel project

In your Laravel app's `composer.json`:

```json
"repositories": [
  {
    "type": "path",
    "url": "packages/YourName/LaravelAcl"
  }
]

Then run:

composer require yourname/laravel-acl:@dev


---

2. Publish and run the migration

php artisan vendor:publish --tag=migrations
php artisan migrate


---

3. Sync permissions

This command will scan all public methods of your controllers and store them in the permissions table:

php artisan acl:sync-permissions


---

4. Use Middleware

Apply check.acl middleware to your routes or controllers:

Route::middleware(['auth', 'check.acl'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});


---

Extending the Package

Add Permission Checks to User Model

public function hasPermission($controller, $method)
{
    $name = strtolower($controller . '.' . $method);

    // Example: Implement role->permissions relationship in your app
    return $this->permissions()->where('name', $name)->exists();
}


---

File Structure

src/
├── Commands/
│   └── SyncPermissions.php
├── Middleware/
│   └── CheckAclPermission.php
├── Models/
│   └── Permission.php
├── Migrations/
│   └── create_permissions_table.php.stub
└── LaravelAclServiceProvider.php


---

License

MIT © 2025 MuhammadNomanRauf
