<?php

namespace Database\Factories;

use App\Models\ParkingLot;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingLotFactory extends Factory
{
    protected $model = ParkingLot::class;

    public function definition()
    {
        return [
            'user_id' => 1, // o User::factory(),
            'name' => $this->faker->company(),
            'location' => $this->faker->address(),
            'capacity' => 20,
            'available_spots' => 20,
            'accepts_cars' => true,
            'accepts_motorcycles' => true,
            'car_hourly_rate' => 3000,
            'motorcycle_hourly_rate' => 1500,
        ];
    }
}
