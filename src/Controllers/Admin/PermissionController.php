<?php

namespace Nrauf90\LaravelAcl\Controllers\Admin;

use App\Http\Controllers\Controller;
use Nrauf90\LaravelAcl\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('roles')->get();
        return view('laravel-acl::admin.permissions.index', compact('permissions'));
    }

    public function sync()
    {
        // Run the sync command
        \Artisan::call('acl:sync-permissions');
        
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permissions synced successfully.');
    }
} 