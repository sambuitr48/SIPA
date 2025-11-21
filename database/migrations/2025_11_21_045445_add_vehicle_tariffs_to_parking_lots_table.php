<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega columnas de tarifas y tipos de vehículo a la tabla parking_lots.
     */
    public function up(): void
    {
        Schema::table('parking_lots', function (Blueprint $table) {

            // Indica si el parqueadero acepta carros
            $table->boolean('accepts_cars')
                ->default(true)
                ->after('total_spots');

            // Indica si acepta motos
            $table->boolean('accepts_motorcycles')
                ->default(true)
                ->after('accepts_cars');

            // Tarifa por hora para carros
            $table->decimal('car_hourly_rate', 10, 2)
                ->nullable()
                ->after('accepts_motorcycles');

            // Tarifa por hora para motos
            $table->decimal('motorcycle_hourly_rate', 10, 2)
                ->nullable()
                ->after('car_hourly_rate');
        });
    }

    /**
     * Quita las columnas en caso de rollback.
     */
    public function down(): void
    {
        Schema::table('parking_lots', function (Blueprint $table) {
            $table->dropColumn([
                'accepts_cars',
                'accepts_motorcycles',
                'car_hourly_rate',
                'motorcycle_hourly_rate'
            ]);
        });
    }
};
