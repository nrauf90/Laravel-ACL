<?php

namespace Nrauf90\LaravelAcl;

use Illuminate\Support\ServiceProvider;
use Nrauf90\LaravelAcl\Middleware\CheckAclPermission;
use Nrauf90\LaravelAcl\Commands\SyncPermissions;

class LaravelAclServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish migration
        $this->publishes([
            __DIR__ . '/Migrations/create_acl_tables.php' =>
                database_path('migrations/' . date('Y_m_d_His') . '_create_acl_tables.php'),
        ], 'laravel-acl');

        // Publish views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'laravel-acl');
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/laravel-acl'),
        ], 'views');

        // Register command
        if ($this->app->runningInConsole()) {
            $this->commands([SyncPermissions::class]);
        }

        // Register middleware
        $this->app['router']->aliasMiddleware('check.acl', CheckAclPermission::class);

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/admin.php');
    }

    public function register()
    {
        //
    }
}
