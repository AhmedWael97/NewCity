<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order
        $this->call([
            UserRoleSeeder::class,
            ArabicCategorySeeder::class,
            EgyptianNewCitiesSeeder::class,
           
        ]);

        // Create a super admin user
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@city.com',
            'password' => Hash::make('superadminpassword'),
            'user_role_id' => 4, // Super Admin role
            'user_type' => 'admin', // Set user type for admin guard
            'is_active' => true,
            'is_verified' => true,
        ]);

        // // Create a test customer
        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'user_role_id' => 1, // Customer role
        ]);

        // // Create a test shop owner
        User::factory()->create([
            'name' => 'Shop Owner',
            'email' => 'shop@example.com',
            'user_role_id' => 2, // Shop Owner role
        ]);


        $this->call(EgyptianShopsSeeder::class);
      //  $this->call(ProductsSeeder::class);
      //  $this->call(ServicesSeeder::class);
      //  $this->call([RatingSeeder::class]);

        
        
    }
}
