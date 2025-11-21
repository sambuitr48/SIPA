<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de parqueaderos administrados por usuarios tipo host.
     */
    public function up(): void
    {
        Schema::create('parking_lots', function (Blueprint $table) {
            $table->id();

            // Usuario propietario del parqueadero (solo usuarios con rol host)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Nombre del parqueadero
            $table->string('name');

            // Dirección física del parqueadero
            $table->string('address')->nullable();

            // Coordenadas geográficas (latitud/longitud)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Capacidad total de cupos en el parqueadero
            $table->integer('total_spots')->default(0);

            // Cupos disponibles (se actualiza al crear reservas)
            $table->integer('available_spots')->default(0);

            // Tarifa base del parqueadero
            $table->decimal('tariff', 10, 2)->default(0);

            // Horario de operación
            $table->string('schedule')->nullable();

            // Estado del parqueadero
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de parqueaderos.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_lots');
    }
};
