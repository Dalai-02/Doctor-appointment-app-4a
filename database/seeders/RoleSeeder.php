<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Define roles
        $roles = [
            'Paciente',
            'Médico',
            'Recepcionista',
            'Administrador',
        ];

        //Crear roles
        foreach ($roles as $role) {
            Role::create(['name' => $role
            ]);
        }
    }
}
