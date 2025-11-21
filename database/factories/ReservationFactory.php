<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\ParkingLot;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'parking_lot_id' => ParkingLot::factory(),
            'vehicle_type' => 'car',
            'hours' => 2,
            'total_price' => 6000,
            'status' => 'confirmed',
            'payment_method' => null,
        ];
    }
}
