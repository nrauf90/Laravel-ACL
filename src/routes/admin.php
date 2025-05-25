<?php

use Illuminate\Support\Facades\Route;
use Nrauf90\LaravelAcl\Controllers\Admin\RoleController;
use Nrauf90\LaravelAcl\Controllers\Admin\PermissionController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    // Role routes
    Route::resource('roles', RoleController::class);

    // Permission routes
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions/sync', [PermissionController::class, 'sync'])->name('permissions.sync');
}); 