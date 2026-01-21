<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage products',
            'create products',
            'edit products',
            'delete products',
            'view products',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role - has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Standard User role - limited permissions
        $userRole = Role::firstOrCreate(['name' => 'Standard User']);
        $userRole->givePermissionTo([
            'view products',
            'create products',
            'edit products', // Only own products (checked in controller)
            'delete products', // Only own products
        ]);

        // Create sample users
        
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole('Admin');

        // Standard user
        $user = User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Standard User',
                'password' => Hash::make('password123'),
            ]
        );
        $user->assignRole('Standard User');

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Admin: admin@gmail.com / password123');
        $this->command->info('User: user@gmail.com / password123');
    }
}