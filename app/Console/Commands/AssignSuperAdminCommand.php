<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-super-admin {user_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign super_admin role to a user for both web and admin guards';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }
        
        // Clear any existing roles
        $user->syncRoles([]);
        
        // Assign super_admin role for web guard
        $webRole = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($webRole) {
            $user->assignRole($webRole);
            $this->info("✓ Assigned super_admin (web guard) to {$user->name}");
        } else {
            $this->warn("✗ super_admin role not found for web guard");
        }
        
        // Assign super_admin role for admin guard
        $adminRole = Role::where('name', 'super_admin')->where('guard_name', 'admin')->first();
        if ($adminRole) {
            $user->assignRole($adminRole);
            $this->info("✓ Assigned super_admin (admin guard) to {$user->name}");
        } else {
            $this->warn("✗ super_admin role not found for admin guard");
        }
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info("✓ Permission cache cleared");
        
        // Show user's roles and permissions count
        $this->newLine();
        $this->info("User: {$user->name} (ID: {$user->id})");
        $this->info("Email: {$user->email}");
        $this->info("Roles: " . $user->roles->pluck('name')->implode(', '));
        $this->info("Total Permissions: " . $user->getAllPermissions()->count());
        
        return 0;
    }
}
