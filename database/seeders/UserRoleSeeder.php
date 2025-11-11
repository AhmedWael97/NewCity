<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Regular app user who can browse shops and services',
                'permissions' => ['view_shops', 'search_shops', 'view_profile'],
                'is_active' => true,
            ],
            [
                'name' => 'Shop Owner',
                'slug' => 'shop_owner',
                'description' => 'Business owner who can manage their shop listings',
                'permissions' => ['view_shops', 'manage_own_shops', 'view_profile', 'edit_profile'],
                'is_active' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with limited administrative privileges',
                'permissions' => ['manage_shops', 'manage_categories', 'manage_cities', 'view_users'],
                'is_active' => true,
            ],
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Super administrator with full system access',
                'permissions' => ['*'],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            \App\Models\UserRole::create($role);
        }
    }
}
