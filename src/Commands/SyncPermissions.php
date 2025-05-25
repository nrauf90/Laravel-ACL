<?php

namespace Nrauf90\LaravelAcl\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;
use Nrauf90\LaravelAcl\Models\Permission;

class SyncPermissions extends Command
{
    protected $signature = 'acl:sync-permissions';
    protected $description = 'Scan controllers and sync permissions';

    public function handle()
    {
        $controllerPath = app_path('Http/Controllers');
        $controllers = File::allFiles($controllerPath);

        foreach ($controllers as $file) {
            $class = 'App\\Http\\Controllers\\' . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

            if (!class_exists($class)) continue;

            $reflection = new ReflectionClass($class);
            if (!$reflection->isSubclassOf('App\Http\Controllers\Controller')) continue;

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->class === $reflection->getName() && $method->name !== '__construct') {
                    Permission::firstOrCreate([
                        'name' => strtolower($reflection->getShortName()) . '.' . $method->name,
                        'controller' => $reflection->getShortName(),
                        'method' => $method->name,
                    ]);
                }
            }
        }

        $this->info("Permissions synced.");
    }
}
