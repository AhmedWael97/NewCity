<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Get all existing permissions from web guard
        $webPermissions = Permission::where('guard_name', 'web')->get();
        
        // Create duplicate permissions for admin guard
        foreach ($webPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->name,
                'guard_name' => 'admin'
            ]);
        }
        
        // Get all existing roles from web guard
        $webRoles = Role::where('guard_name', 'web')->get();
        
        // Create duplicate roles for admin guard and sync their permissions
        foreach ($webRoles as $webRole) {
            $adminRole = Role::firstOrCreate([
                'name' => $webRole->name,
                'guard_name' => 'admin'
            ]);
            
            // Get permission names from web role
            $permissionNames = $webRole->permissions->pluck('name')->toArray();
            
            // Get admin guard permissions with same names
            $adminPermissions = Permission::where('guard_name', 'admin')
                ->whereIn('name', $permissionNames)
                ->get();
            
            // Sync permissions to admin role
            $adminRole->syncPermissions($adminPermissions);
        }
        
        // Assign admin guard roles to users who have web guard roles
        $users = \App\Models\User::whereHas('roles', function($query) {
            $query->where('guard_name', 'web');
        })->get();
        
        foreach ($users as $user) {
            $webRoles = $user->roles()->where('guard_name', 'web')->get();
            
            foreach ($webRoles as $webRole) {
                // Assign the same role for admin guard
                if (!$user->hasRole($webRole->name, 'admin')) {
                    $user->assignRole($webRole->name, 'admin');
                }
            }
        }
        
        // Clear permission cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all admin guard permissions and roles
        Permission::where('guard_name', 'admin')->delete();
        Role::where('guard_name', 'admin')->delete();
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
