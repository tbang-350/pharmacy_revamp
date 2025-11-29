<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::create(['name' => 'Admin']);
        $manager = Role::create(['name' => 'Manager']);
        $cashier = Role::create(['name' => 'Cashier']);

        // Create permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage categories',
            'manage suppliers',
            'manage products',
            'make sales',
            'view sales',
            'make purchases',
            'view purchases',
            'manage stock',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        
        $manager->givePermissionTo([
            'manage categories',
            'manage suppliers',
            'manage products',
            'make sales',
            'view sales',
            'make purchases',
            'view purchases',
            'manage stock',
            'view reports',
        ]);

        $cashier->givePermissionTo([
            'make sales',
            'view sales',
            'view purchases',
        ]);
    }
}
