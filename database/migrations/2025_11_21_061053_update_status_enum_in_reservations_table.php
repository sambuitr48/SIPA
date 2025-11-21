<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Actualiza los valores permitidos del campo status en reservations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Cambiar ENUM existente a uno nuevo con más valores
            $table->enum('status', [
                'pending_payment',
                'paid',
                'cancelled',
                'confirmed'
            ])->default('pending_payment')->change();
        });
    }

    /**
     * Reversa los cambios del ENUM.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Restaurar el ENUM original si es necesario
            $table->enum('status', ['confirmed', 'cancelled'])
                ->default('confirmed')
                ->change();
        });
    }
};
