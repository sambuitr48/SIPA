<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    
    /**
     * Campos asignables en masa.
     */
    protected $fillable = [
        'user_id',
        'parking_lot_id',
        'vehicle_type',
        'hours',
        'total_price',
        'status',
        'payment_method',
    ];

    /**
     * Relación: la reserva pertenece a un usuario (driver).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: la reserva pertenece a un parqueadero.
     */
    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class);
    }
}
