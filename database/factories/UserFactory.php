<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => 'driver',   // 🔥 ALELUYA
            'phone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'),
        ];
    }

    public function driver()
    {
        return $this->state(fn () => ['role' => 'driver']);
    }

    public function host()
    {
        return $this->state(fn () => ['role' => 'host']);
    }
}
