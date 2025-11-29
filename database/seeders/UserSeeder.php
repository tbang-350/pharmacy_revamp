<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@pharmacy.com',
            'password' => Hash::make('password'),
            'phone' => '0700000000',
            'is_active' => true,
        ]);

        $admin->assignRole('Admin');
    }
}
