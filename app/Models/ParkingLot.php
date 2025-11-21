<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLot extends Model
{
    use HasFactory;
    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'total_spots',
        'available_spots',
        'accepts_cars',
        'accepts_motorcycles',
        'car_hourly_rate',
        'motorcycle_hourly_rate',
        'schedule',
        'status',
    ];

    /**
     * Relación: un parqueadero pertenece a un usuario host.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: un parqueadero puede tener muchas reservas.
     */
    public function reservations()
    {
        return $this->hasMany(\App\Models\Reservation::class);
    }

}
