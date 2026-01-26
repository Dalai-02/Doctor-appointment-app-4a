<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a guest cannot delete a user account', function () {
    //1) Crear usuario
    $user = User::factory()->create();

    //2) Intentar borrar sin autenticación
    $response = $this->delete(route('admin.users.destroy', $user));

    //3) Esperar redirección o no autorizado
    $response->assertStatus(302);

    //4) Verificar que sigue en la BD
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});
