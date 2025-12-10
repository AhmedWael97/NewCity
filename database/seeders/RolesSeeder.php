<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles for web guard
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        $cityManager = Role::firstOrCreate(['name' => 'city_manager', 'guard_name' => 'web']);
        
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        // Get all permissions for web guard
        $allPermissionsWeb = Permission::where('guard_name', 'web')->get();

        // Super Admin - All permissions
        $superAdmin->syncPermissions($allPermissionsWeb);

        // Admin - All permissions except role/user management
        $adminPermissions = Permission::where('guard_name', 'web')
            ->where('name', 'not like', 'assign-roles')
            ->where('name', 'not like', 'assign-cities')
            ->where('name', 'not like', 'create-roles')
            ->where('name', 'not like', 'edit-roles')
            ->where('name', 'not like', 'delete-roles')
            ->get();
        $admin->syncPermissions($adminPermissions);

        // City Manager - Can manage everything in their assigned cities
        $cityManagerPermissionNames = [
            'view-dashboard',
            'view-statistics',
            
            'view-shops',
            'create-shops',
            'edit-shops',
            'delete-shops',
            'verify-shops',
            'feature-shops',
            'toggle-shop-status',
            
            'view-shop-suggestions',
            'approve-shop-suggestions',
            'reject-shop-suggestions',
            
            'view-news',
            'create-news',
            'edit-news',
            'delete-news',
            'publish-news',
            
            'view-banners',
            'create-banners',
            'edit-banners',
            'delete-banners',
            'toggle-banner-status',
            
            'view-user-services',
            'edit-user-services',
            'verify-user-services',
            'feature-user-services',
            
            'view-marketplace',
            'edit-marketplace-items',
            'verify-marketplace-items',
            
            'view-reviews',
            'moderate-reviews',
            
            'view-tickets',
            'reply-tickets',
            
            'view-analytics',
        ];
        
        $cityManagerPermissions = Permission::where('guard_name', 'web')
            ->whereIn('name', $cityManagerPermissionNames)->get();
        $cityManager->syncPermissions($cityManagerPermissions);

        // Editor - Can create and edit content but not delete
        $editorPermissionNames = [
            'view-dashboard',
            
            'view-shops',
            'create-shops',
            'edit-shops',
            
            'view-news',
            'create-news',
            'edit-news',
            
            'view-banners',
            'create-banners',
            'edit-banners',
            
            'view-user-services',
            'edit-user-services',
            
            'view-marketplace',
            'create-marketplace-items',
            'edit-marketplace-items',
            
            'view-reviews',
        ];
        
        $editorPermissions = Permission::where('guard_name', 'web')
            ->whereIn('name', $editorPermissionNames)->get();
        $editor->syncPermissions($editorPermissions);

        // Viewer - Read-only access
        $viewerPermissions = Permission::where('guard_name', 'web')
            ->where('name', 'like', 'view-%')->get();
        $viewer->syncPermissions($viewerPermissions);

        // Assign super_admin role to existing admin users
        User::where('email', 'like', '%admin%')
            ->orWhere('user_role_id', 1)
            ->each(function ($user) use ($superAdmin) {
                $user->assignRole($superAdmin);
            });

        $this->command->info('Created 5 roles and assigned permissions');
        $this->command->info('Roles: super_admin, admin, city_manager, editor, viewer');
    }
}
