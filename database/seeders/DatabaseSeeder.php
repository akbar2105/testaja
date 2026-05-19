<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            KabupatenSeeder::class,
            KecamatanSeeder::class,
            RekapHarianPanenSeeder::class,
            RekapHarianTanamSeeder::class,
        ]);
    }
}