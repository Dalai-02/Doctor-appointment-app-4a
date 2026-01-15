<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            //Crea un usuario de prueba
        User::factory()->create([
            'name' => 'Dalai Pacheco',
            'email' => 'dalai@gmail.com',
            'password' => bcrypt('12345678'),
            'id_number' => '1234567890',
            'phone' => '555-1234',
            'address' => '123 Main St, City, Country',
        ])->assignRole('Médico');
    }
}
