<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by module
        $permissions = [
            // Dashboard
            'view-dashboard',
            'view-statistics',
            
            // Cities
            'view-cities',
            'create-cities',
            'edit-cities',
            'delete-cities',
            'toggle-city-status',
            
            // Categories
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            
            // Shops
            'view-shops',
            'create-shops',
            'edit-shops',
            'delete-shops',
            'verify-shops',
            'feature-shops',
            'toggle-shop-status',
            
            // Shop Suggestions
            'view-shop-suggestions',
            'approve-shop-suggestions',
            'reject-shop-suggestions',
            'delete-shop-suggestions',
            
            // City Suggestions
            'view-city-suggestions',
            'approve-city-suggestions',
            'reject-city-suggestions',
            'delete-city-suggestions',
            
            // News
            'view-news',
            'create-news',
            'edit-news',
            'delete-news',
            'publish-news',
            
            // Banners
            'view-banners',
            'create-banners',
            'edit-banners',
            'delete-banners',
            'toggle-banner-status',
            
            // User Services
            'view-user-services',
            'create-user-services',
            'edit-user-services',
            'delete-user-services',
            'verify-user-services',
            'feature-user-services',
            
            // Service Categories
            'view-service-categories',
            'create-service-categories',
            'edit-service-categories',
            'delete-service-categories',
            
            // Users
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'toggle-user-status',
            'assign-roles',
            'assign-cities',
            
            // Roles & Permissions
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'assign-permissions',
            
            // App Settings
            'view-app-settings',
            'edit-app-settings',
            'manage-notifications',
            'send-notifications',
            
            // Advertisements
            'view-advertisements',
            'create-advertisements',
            'edit-advertisements',
            'delete-advertisements',
            
            // Support Tickets
            'view-tickets',
            'reply-tickets',
            'close-tickets',
            'assign-tickets',
            
            // Forum
            'view-forum',
            'create-forum-topics',
            'edit-forum-topics',
            'delete-forum-topics',
            'moderate-forum',
            
            // Marketplace
            'view-marketplace',
            'create-marketplace-items',
            'edit-marketplace-items',
            'delete-marketplace-items',
            'verify-marketplace-items',
            
            // Reviews
            'view-reviews',
            'moderate-reviews',
            'delete-reviews',
            
            // Analytics
            'view-analytics',
            'export-analytics',
        ];

        // Create all permissions for both web and admin guards
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $this->command->info('Created ' . count($permissions) . ' permissions for both web and admin guards');
    }
}
