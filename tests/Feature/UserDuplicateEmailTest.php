<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

// Usar la función para refrescar la base de datos en cada prueba
uses(RefreshDatabase::class);

test('a user cannot be created with a duplicate email', function () {
   //1) Crear un usuario existente
   $existingUser = User::factory()->create([
       'email' => 'test@email.com'
   ]);

   //2) Crear un rol válido 
   $role = Role::create(['name' => 'Admin']);

   //3) Simular que ese usuario ha iniciado sesión
   $this->actingAs($existingUser);

   //4) Simular una petición HTTP POST con email duplicado
   $response = $this->postJson(route('admin.users.store'), [
       'name' => 'Nuevo Usuario',
       'email' => 'test@email.com', // Email duplicado
       'password' => 'password123',
       'password_confirmation' => 'password123',
       'id_number' => 'ID-999999',
       'phone' => '9999999999',
       'address' => 'Calle falsa 123',
       'role_id' => $role->id,
   ]);

   //5) Esperar error de validación 
   $response->assertStatus(422);

   //6) Verificar que NO se creó otro usuario
   $this->assertDatabaseCount('users', 1);
});
