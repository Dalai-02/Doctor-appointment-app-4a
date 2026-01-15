<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea las tablas users, password_reset_tokens y sessions
     */
    public function up(): void
    {
        // ==============================
        // TABLA USERS
        // ==============================
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('name'); // Nombre del usuario
            $table->string('email')->unique(); // Email único
            $table->timestamp('email_verified_at')->nullable(); // Fecha de verificación del correo
            $table->string('password'); // Contraseña

            // Campos personalizados
            $table->string('id_number')->unique(); // Número de identificación único
            $table->string('phone'); // Teléfono
            $table->string('address'); // Dirección

            $table->rememberToken(); // Token de sesión
            $table->foreignId('current_team_id')->nullable(); // (Jetstream) equipo actual
            $table->string('profile_photo_path', 2048)->nullable(); // Ruta foto de perfil
            $table->timestamps(); // created_at y updated_at
        });

        // ==============================
        // TABLA password_reset_tokens
        // ==============================
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email como llave primaria
            $table->string('token'); // Token de recuperación
            $table->timestamp('created_at')->nullable(); // Fecha de creación
        });

        // ==============================
        // TABLA sessions
        // ==============================
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID de la sesión
            $table->foreignId('user_id')->nullable()->index(); // Usuario dueño de la sesión
            $table->string('ip_address', 45)->nullable(); // Dirección IP
            $table->text('user_agent')->nullable(); // Datos del navegador
            $table->longText('payload'); // Datos de sesión
            $table->integer('last_activity')->index(); // Última actividad
        });
    }

    /**
     * Reverse the migrations.
     * Elimina las tablas en orden inverso
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
