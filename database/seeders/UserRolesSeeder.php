<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\City;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a city for users
        $city = City::first();
        if (!$city) {
            $city = City::create([
                'name' => 'الرياض',
                'slug' => 'riyadh',
                'country' => 'السعودية',
                'state' => 'الرياض',
                'description' => 'العاصمة السعودية',
                'is_active' => true,
            ]);
        }

        // Get user roles
        $userRole = \App\Models\UserRole::where('slug', 'user')->first();
        $shopOwnerRole = \App\Models\UserRole::where('slug', 'shop_owner')->first();
        $adminRole = \App\Models\UserRole::where('slug', 'admin')->first();

        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدير النظام',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'user_role_id' => $adminRole->id,
                'is_active' => true,
                'city_id' => $city->id,
                'phone' => '0501234567',
                'email_verified_at' => Carbon::now(),
            ]
        );

        // Create Shop Owner User
        User::updateOrCreate(
            ['email' => 'shopowner@example.com'],
            [
                'name' => 'صاحب متجر',
                'email' => 'shopowner@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'shop_owner',
                'user_role_id' => $shopOwnerRole->id,
                'is_active' => true,
                'city_id' => $city->id,
                'phone' => '0507654321',
                'email_verified_at' => Carbon::now(),
            ]
        );

        // Create Regular User
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'مستخدم عادي',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'regular',
                'user_role_id' => $userRole->id,
                'is_active' => true,
                'city_id' => $city->id,
                'phone' => '0509876543',
                'email_verified_at' => Carbon::now(),
            ]
        );

        // Create additional test users
        User::updateOrCreate(
            ['email' => 'test.admin@city.com'],
            [
                'name' => 'أحمد المدير',
                'email' => 'test.admin@city.com',
                'password' => Hash::make('admin123'),
                'user_type' => 'admin',
                'user_role_id' => $adminRole->id,
                'is_active' => true,
                'city_id' => $city->id,
                'phone' => '0501111111',
                'email_verified_at' => Carbon::now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'test.shopowner@city.com'],
            [
                'name' => 'محمد صاحب المتجر',
                'email' => 'test.shopowner@city.com',
                'password' => Hash::make('shop123'),
                'user_type' => 'shop_owner',
                'user_role_id' => $shopOwnerRole->id,
                'is_active' => true,
                'city_id' => $city->id,
                'phone' => '0502222222',
                'email_verified_at' => Carbon::now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'test.user@city.com'],
            [
                'name' => 'فاطمة المستخدمة',
                'email' => 'test.user@city.com',
                'password' => Hash::make('user123'),
                'user_type' => 'regular',
                'user_role_id' => $userRole->id,
                'is_active' => true,
                'city_id' => $city->id,
                'phone' => '0503333333',
                'email_verified_at' => Carbon::now(),
            ]
        );

        $this->command->info('Users with different roles created successfully!');
        $this->command->line('Admin: admin@example.com / password');
        $this->command->line('Shop Owner: shopowner@example.com / password');
        $this->command->line('User: user@example.com / password');
        $this->command->line('Test Admin: test.admin@city.com / admin123');
        $this->command->line('Test Shop Owner: test.shopowner@city.com / shop123');
        $this->command->line('Test User: test.user@city.com / user123');
    }
}