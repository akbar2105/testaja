<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'master_admin',
                'display_name' => 'Master Admin',
                'description' => 'Full access to system, can manage admins'
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Can manage users and all data entries'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}