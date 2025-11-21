<?php

namespace App\Services;

use App\Models\ParkingLot;
use App\Models\Reservation;
use App\Models\User;
use App\Repositories\ReservationRepository;
use App\Repositories\ParkingLotRepository;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    protected ReservationRepository $reservations;
    protected ParkingLotRepository $parkingLots;

    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(
    ReservationRepository $reservations,
    ParkingLotRepository $parkingLots
    ) {
        $this->reservations = $reservations;
        $this->parkingLots = $parkingLots;
    }


    /**
     * Crea una reserva para un usuario driver.
     * Aplica reglas de negocio: disponibilidad, tipo de vehículo y tarifas.
     */
    public function createReservation(User $user, array $data): Reservation
    {
        return DB::transaction(function () use ($user, $data) {

            // Buscar parqueadero
            $parkingLot = $this->parkingLots->find($data['parking_lot_id']);

            if (! $parkingLot || $parkingLot->status !== 'active') {
                throw new \RuntimeException('El parqueadero no está disponible.');
            }

            // Validar disponibilidad básica
            if ($parkingLot->available_spots <= 0) {
                throw new \RuntimeException('No hay cupos disponibles en este parqueadero.');
            }

            // Validar tipo de vehículo y tarifa
            $vehicleType = $data['vehicle_type'];
            $hours = $data['hours'];

            if ($vehicleType === 'car') {
                if (! $parkingLot->accepts_cars || is_null($parkingLot->car_hourly_rate)) {
                    throw new \RuntimeException('Este parqueadero no acepta carros.');
                }
                $rate = $parkingLot->car_hourly_rate;
            } elseif ($vehicleType === 'motorcycle') {
                if (! $parkingLot->accepts_motorcycles || is_null($parkingLot->motorcycle_hourly_rate)) {
                    throw new \RuntimeException('Este parqueadero no acepta motos.');
                }
                $rate = $parkingLot->motorcycle_hourly_rate;
            } else {
                throw new \RuntimeException('Tipo de vehículo no válido.');
            }

            // Calcular precio total
            $totalPrice = $hours * $rate;

            // Crear reserva
            $reservation = $this->reservations->create([
                'user_id'        => $user->id,
                'parking_lot_id' => $parkingLot->id,
                'vehicle_type'   => $vehicleType,
                'hours'          => $hours,
                'total_price'    => $totalPrice,
                'status'         => 'confirmed',
            ]);

            // Actualizar disponibilidad del parqueadero
            $parkingLot->available_spots = max(0, $parkingLot->available_spots - 1);
            $parkingLot->save();

            return $reservation;
        });
    }

    /**
     * Obtiene todas las reservas de un usuario driver.
     */
    public function getReservationsForUser(User $user)
    {
        return $this->reservations->getByUser($user->id);
    }

    /**
     * Cancela una reserva si pertenece al usuario y ajusta la disponibilidad.
     */
    public function cancelReservation(Reservation $reservation, User $user): Reservation
    {
        return DB::transaction(function () use ($reservation, $user) {

            if ($reservation->user_id !== $user->id) {
                throw new \RuntimeException('No está autorizado para cancelar esta reserva.');
            }

            if ($reservation->status !== 'confirmed') {
                throw new \RuntimeException('Solo se pueden cancelar reservas confirmadas.');
            }

            // Cambiar estado de la reserva
            $reservation = $this->reservations->update($reservation, [
                'status' => 'cancelled',
            ]);

            // Devolver el cupo al parqueadero
            $parkingLot = $reservation->parkingLot;
            if ($parkingLot) {
                $parkingLot->available_spots = $parkingLot->available_spots + 1;
                $parkingLot->save();
            }

            return $reservation;
        });
    }
    
    /**
     * Procesa pago simulado.
     */
    public function pay(Reservation $reservation, array $data): Reservation
    {
        return $this->reservations->update($reservation, [
            'status' => 'paid',
            'payment_method' => $data['payment_method'],
        ]);
    }
}
