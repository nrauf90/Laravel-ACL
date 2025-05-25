<?php

use Illuminate\Support\Facades\Route;
use Nrauf90\LaravelAcl\Controllers\Admin\RoleController;
use Nrauf90\LaravelAcl\Controllers\Admin\PermissionController;

Route::middleware(['web', 'auth', 'check.acl'])->prefix('admin')->name('admin.')->group(function () {
    // Role Management
    Route::resource('roles', RoleController::class);

    // Permission Management
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions/sync', [PermissionController::class, 'sync'])->name('permissions.sync');
}); 