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
        // Clear permission cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Run seeders in order
        $this->call([
           // UserRoleSeeder::class,
            PermissionsSeeder::class,  // Seed permissions first (for both guards)
            RolesSeeder::class,         // Then seed roles (for both guards)
            //ArabicCategorySeeder::class,
            //EgyptianNewCitiesSeeder::class,
        ]);

        // Create a super admin user with admin guard
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@city.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'user_role_id' => 4, // Super Admin role from user_roles table
                'user_type' => 'admin', // Set user type for admin guard
                'is_active' => true,
                'is_verified' => true,
            ]
        );
        
        // Assign super_admin role for both web and admin guards
        // This ensures the user has permissions on both the API/frontend (web) and admin panel (admin)
        if (!$superAdmin->hasRole('super_admin', 'web')) {
            $superAdmin->assignRole(\Spatie\Permission\Models\Role::where('name', 'super_admin')->where('guard_name', 'web')->first());
        }
        if (!$superAdmin->hasRole('super_admin', 'admin')) {
            $superAdmin->assignRole(\Spatie\Permission\Models\Role::where('name', 'super_admin')->where('guard_name', 'admin')->first());
        }
        
        $this->command->info('✓ Super Admin user created/updated with super_admin role for both guards');
        
        // Clear permission cache again after all assignments
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // // Create a test customer
        // User::factory()->create([
        //     'name' => 'Test Customer',
        //     'email' => 'customer@example.com',
        //     'user_role_id' => 1, // Customer role
        // ]);

        // // // Create a test shop owner
        // User::factory()->create([
        //     'name' => 'Shop Owner',
        //     'email' => 'shop@example.com',
        //     'user_role_id' => 2, // Shop Owner role
        // ]);


       // $this->call(EgyptianShopsSeeder::class);
      //  $this->call(ProductsSeeder::class);
      //  $this->call(ServicesSeeder::class);
      //  $this->call([RatingSeeder::class]);

        
        
    }
}
