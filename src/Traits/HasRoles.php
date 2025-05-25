<?php

namespace Nrauf90\LaravelAcl\Traits;

use Nrauf90\LaravelAcl\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    /**
     * Get the roles associated with the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role')
            ->withTimestamps();
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Check if the user has all of the given roles.
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->count() === count($roles);
    }

    /**
     * Assign roles to the user.
     */
    public function assignRoles(array $roles): void
    {
        $this->roles()->sync($roles);
    }

    /**
     * Remove roles from the user.
     */
    public function removeRoles(array $roles): void
    {
        $this->roles()->detach($roles);
    }
} 