<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de reservas realizadas por conductores.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // Usuario que realiza la reserva (debe ser driver)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Parqueadero reservado
            $table->foreignId('parking_lot_id')
                ->constrained('parking_lots')
                ->onDelete('cascade');

            // Tipo de vehículo: carro o moto
            $table->enum('vehicle_type', ['car', 'motorcycle']);

            // Cantidad de horas reservadas
            $table->integer('hours')->unsigned();

            // Precio total calculado para la reserva
            $table->decimal('total_price', 10, 2);

            // Estado de la reserva
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])
                ->default('confirmed');

            $table->timestamps();
        });
    }

    /**
     * Elimina la tabla de reservas.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
