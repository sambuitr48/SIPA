<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la columna payment_method a la tabla reservations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Método de pago simulado: cash, credit_card, debit_card, nequi, daviplata
            $table->string('payment_method')->nullable()->after('status');
        });
    }

    /**
     * Reversa los cambios de la migración (rollback).
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
