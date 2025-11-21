<?php

namespace App\Repositories;

use App\Models\Reservation;

class ReservationRepository
{
    /**
     * Crea una nueva reserva.
     */
    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    /**
     * Busca una reserva por ID.
     */
    public function find(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    /**
     * Obtiene todas las reservas de un usuario driver.
     */
    public function getByUser(int $userId)
    {
        return Reservation::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Actualiza una reserva existente.
     */
    public function update(Reservation $reservation, array $data): Reservation
    {
        $reservation->update($data);
        return $reservation;
    }
}
