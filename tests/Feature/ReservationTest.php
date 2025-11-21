<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ParkingLot;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private function createDriver(): User
    {
        return User::factory()->create([
            'role' => 'driver',
        ]);
    }

    private function createParkingLot(): ParkingLot
    {
        return ParkingLot::create([
            'user_id' => User::factory()->create(['role' => 'host'])->id, 
            'name' => 'Parqueadero Central',
            'location' => 'Armenia',
            'capacity' => 20,
            'available_spots' => 20,
            'accepts_cars' => true,
            'accepts_motorcycles' => true,
            'car_hourly_rate' => 3000,
            'motorcycle_hourly_rate' => 1500,
        ]);
    }


    /** @test */
    public function a_driver_can_create_a_reservation()
    {
        $user = $this->createDriver();
        $parking = $this->createParkingLot();

        $this->actingAs($user);

        $response = $this->postJson('/api/reservations', [
            'parking_lot_id' => $parking->id,
            'vehicle_type' => 'car',
            'hours' => 2
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'parking_lot_id' => $parking->id,
        ]);
    }

    /** @test */
    public function a_driver_can_view_his_reservations()
    {
        $user = $this->createDriver();
        $parking = $this->createParkingLot();

        Reservation::create([
            'user_id' => $user->id,
            'parking_lot_id' => $parking->id,
            'vehicle_type' => 'car',
            'hours' => 1,
            'total_price' => 3000,
            'status' => 'confirmed',
        ]);

        Reservation::create([
            'user_id' => $user->id,
            'parking_lot_id' => $parking->id,
            'vehicle_type' => 'motorcycle',
            'hours' => 2,
            'total_price' => 3000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($user);

        $response = $this->getJson('/api/reservations');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function a_driver_can_cancel_his_reservation()
    {
        $user = $this->createDriver();
        $parking = $this->createParkingLot();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'parking_lot_id' => $parking->id,
            'vehicle_type' => 'car',
            'hours' => 2,
            'total_price' => 6000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/api/reservations/{$reservation->id}/cancel");

        $response->assertStatus(200);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'cancelled'
        ]);
    }

    /** @test */
    public function a_driver_can_pay_a_reservation()
    {
        $user = $this->createDriver();
        $parking = $this->createParkingLot();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'parking_lot_id' => $parking->id,
            'vehicle_type' => 'car',
            'hours' => 1,
            'total_price' => 3000,
            'status' => 'pending_payment',
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/api/reservations/{$reservation->id}/pay", [
            'payment_method' => 'nequi'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'paid',
            'payment_method' => 'nequi'
        ]);
    }
}
