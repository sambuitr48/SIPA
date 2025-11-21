<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;   // ← IMPORTANTE

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;  // ← AGREGA HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone' 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relación: un usuario host puede administrar varios parqueaderos.
     */
    public function parkingLots()
    {
        return $this->hasMany(ParkingLot::class, 'user_id');
    }

    /**
     * Relación: un usuario driver puede tener muchas reservas.
     */
    public function reservations()
    {
        return $this->hasMany(\App\Models\Reservation::class);
    }

}
