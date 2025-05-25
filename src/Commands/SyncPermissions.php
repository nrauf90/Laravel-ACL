<?php

namespace Nrauf90\LaravelAcl\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;
use Nrauf90\LaravelAcl\Models\Permission;

class SyncPermissions extends Command
{
    protected $signature = 'acl:sync-permissions {--path= : Path to scan for controllers (relative to vendor directory)}';
    protected $description = 'Scan controllers and sync permissions';

    public function handle()
    {
        $updatedCount = 0;
        $createdCount = 0;

        // Scan app controllers
        $this->info("Scanning app controllers...");
        $this->scanControllers(app_path('Http/Controllers'), 'App\\Http\\Controllers\\', $updatedCount, $createdCount);

        // Scan vendor controllers if path is provided
        if ($path = $this->option('path')) {
            $vendorPath = base_path('vendor/' . $path);
            if (File::isDirectory($vendorPath)) {
                $this->info("\nScanning vendor controllers in: " . $path);
                $this->scanVendorControllers($vendorPath, $path, $updatedCount, $createdCount);
            } else {
                $this->error("Vendor path not found: " . $vendorPath);
            }
        }

        $this->info("\nPermissions synced successfully.");
        $this->info("Created: {$createdCount}");
        $this->info("Updated: {$updatedCount}");
    }

    private function scanVendorControllers($basePath, $vendorPath, &$updatedCount, &$createdCount)
    {
        // Find all src directories
        $srcDirs = $this->findSrcDirectories($basePath);
        foreach ($srcDirs as $srcDir => $items) {
            $this->info("Item: " . print_r($items, true));
            foreach ($items as $item) {
            $controllersDir = $item . '/Controllers';
            if (File::isDirectory($controllersDir)) {
                $relativePath = str_replace(base_path('vendor/') . $vendorPath . '/', '', $item);
                $dirName = $this->kebabToCamel(basename($srcDir));
                $namespace = $this->kebabToCamel($vendorPath) . '\\' . $dirName . '\\Controllers\\';
                
                $this->info("Scanning controllers in: " . $relativePath . '/Controllers');
                $this->scanControllers($controllersDir, $namespace, $updatedCount, $createdCount);
            }
            }
        }
    }

    private function kebabToCamel($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    private function findSrcDirectories($path)
    {
        $srcDirs = [];

        if (File::isDirectory($path)) {
            $items = File::directories($path);
            
            foreach ($items as $item) {
                if (basename($item) === 'src') {
                    $srcDirs[basename($path)][] = $item;
                } else {
                    $srcDirs = array_merge($srcDirs, $this->findSrcDirectories($item));
                }
            }
        }
        
        return $srcDirs;
    }

    private function scanControllers($path, $namespace, &$updatedCount, &$createdCount)
    {
        $controllers = File::allFiles($path);

        foreach ($controllers as $file) {
            $this->info("Controllers here: " . $file->getRelativePathname());
            $this->info("NameSpace: " . $namespace);
            $class = $namespace . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());
            if (!class_exists($class)) continue;

            $reflection = new ReflectionClass($class);
            if (!$reflection->isSubclassOf('App\Http\Controllers\Controller')) continue;

            $this->info("Processing controller: " . $reflection->getShortName());

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->class === $reflection->getName() && $method->name !== '__construct') {
                    // Skip if method has @acl-ignore annotation
                    $docComment = $method->getDocComment();
                    
                    $this->info("Method: " . $method->name);

                    if ($docComment && strpos($docComment, '@acl-ignore') !== false) {
                        $this->info("Skipping method (has @acl-ignore)");
                        continue;
                    }

                    // Get title and description from annotations
                    $title = $this->getAnnotationValue($docComment, '@acl-title');
                    $description = $this->getAnnotationValue($docComment, '@acl-description');

                    $permissionName = strtolower($reflection->getShortName()) . '.' . $method->name;

                    // Try to find existing permission
                    $permission = Permission::where('name', $permissionName)->first();

                    if ($permission) {
                        // Update existing permission
                        $permission->update([
                            'title' => $title ?: ucfirst($method->name),
                            'description' => $description ?: '',
                        ]);
                        $this->info("Updated permission: " . $permissionName);
                        $updatedCount++;
                    } else {
                        // Create new permission
                        Permission::create([
                            'name' => $permissionName,
                            'controller' => $reflection->getShortName(),
                            'method' => $method->name,
                            'title' => $title ?: ucfirst($method->name),
                            'description' => $description ?: '',
                        ]);
                        $this->info("Created permission: " . $permissionName);
                        $createdCount++;
                    }
                }
            }
        }
    }

    private function getAnnotationValue($docComment, $annotation)
    {
        if (!$docComment) {
            $this->info("No docblock found");
            return null;
        }

        // Remove comment markers and split into lines
        $lines = preg_split('/\r\n|\r|\n/', $docComment);
        
        foreach ($lines as $line) {
            $line = trim($line, " \t\n\r\0\x0B*/");
            
            if (strpos($line, $annotation) === 0) {
                // Extract the value after the annotation
                $value = trim(substr($line, strlen($annotation)));
                return $value ?: null;
            }
        }

        $this->info("No annotation found for: " . $annotation);
        return null;
    }
}
