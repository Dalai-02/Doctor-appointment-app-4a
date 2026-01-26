<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

// Refrescar la base de datos en cada prueba
uses(RefreshDatabase::class);

test('a user cannot be updated with an invalid phone number', function () {

    //1) Crear rol válido
    $role = Role::create(['name' => 'Admin']);

    //2) Crear usuario autenticado (admin)
    $admin = User::factory()->create();
    $admin->roles()->attach($role);

    //3) Crear usuario a modificar
    $user = User::factory()->create([
        'phone' => '9999999999'
    ]);

    //4) Simular login 
    $this->actingAs($user);

    //5) Enviar actualización con teléfono inválido (letras y muy corto)
    $response = $this->putJson(route('admin.users.update', $user), [
        'name' => 'Usuario Editado',
        'email' => $user->email,
        'id_number' => $user->id_number,
        'phone' => 'ABC123', // Teléfono inválido
        'address' => 'Nueva dirección válida',
        'role_id' => $role->id,
    ]);

    //6) Esperar error de validación
    $response->assertStatus(422);

    //7) Verificar que el teléfono NO cambió en la BD
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'phone' => '9999999999',
    ]);
});
