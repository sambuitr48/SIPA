<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de usuarios con los campos necesarios para la API.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Nombre completo del usuario
            $table->string('name');

            // Correo único
            $table->string('email')->unique();

            // Contraseña hasheada
            $table->string('password');

            // Rol del usuario (admin o user)
            $table->enum('role', ['driver', 'host'])->default('driver');

            // Teléfono opcional
            $table->string('phone')->nullable();

            // Timestamps de Laravel
            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de usuarios.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
