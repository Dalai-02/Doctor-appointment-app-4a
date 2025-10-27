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
        $this->call(
            RoleSeeder::class);

            //Crea un usuario de prueba
        User::factory()->create([
            'name' => 'Dalai Pacheco',
            'email' => 'dalai@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
    }
}
