<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Master Admin
        User::create([
            'name' => 'Master Admin',
            'email' => 'masteradmin@rekappadi.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'is_active' => true
        ]);

        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@rekappadi.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'is_active' => true
        ]);
    }
}