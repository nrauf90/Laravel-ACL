<?php

namespace Nrauf90\LaravelAcl\Traits;

use Nrauf90\LaravelAcl\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    /**
     * Get the permissions associated with the user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
            ->withTimestamps();
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $controller, string $method): bool
    {
        // Check direct permissions
        $hasDirectPermission = $this->permissions()
            ->where('controller', $controller)
            ->where('method', $method)
            ->exists();

        if ($hasDirectPermission) {
            return true;
        }

        // Check permissions through roles
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($controller, $method) {
                $query->where('controller', $controller)
                    ->where('method', $method);
            })
            ->exists();
    }

    /**
     * Check if the user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission['controller'], $permission['method'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission['controller'], $permission['method'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Assign permissions to the user.
     */
    public function assignPermissions(array $permissions): void
    {
        $this->permissions()->sync($permissions);
    }

    /**
     * Remove permissions from the user.
     */
    public function removePermissions(array $permissions): void
    {
        $this->permissions()->detach($permissions);
    }
} 