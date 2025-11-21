<?php

namespace App\Repositories;

use App\Models\ParkingLot;

class ParkingLotRepository
{
    /**
     * Crea un nuevo parqueadero.
     */
    public function create(array $data): ParkingLot
    {
        return ParkingLot::create($data);
    }

    /**
     * Obtiene un parqueadero por ID.
     */
    public function find(int $id): ?ParkingLot
    {
        return ParkingLot::find($id);
    }

    /**
     * Obtiene todos los parqueaderos de un host específico.
     */
    public function getByUser(int $userId)
    {
        return ParkingLot::where('user_id', $userId)->get();
    }

    /**
     * Actualiza un parqueadero existente.
     */
    public function update(ParkingLot $parkingLot, array $data): ParkingLot
    {
        $parkingLot->update($data);
        return $parkingLot;
    }

    /**
     * Elimina un parqueadero.
     */
    public function delete(ParkingLot $parkingLot): bool
    {
        return $parkingLot->delete();
    }
}
