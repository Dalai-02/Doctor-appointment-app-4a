<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar al role seeder creado
        $this->call([
            BloodTypeSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SpecialitySeeder::class
        ]);
    }
}
