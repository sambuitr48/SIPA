<?php

namespace App\Services;

use App\Repositories\ParkingLotRepository;
use App\Models\ParkingLot;

class ParkingLotService
{
    protected ParkingLotRepository $repository;

    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(ParkingLotRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crea un parqueadero asociado a un usuario host.
     */
    public function create(array $data): ParkingLot
    {
        return $this->repository->create($data);
    }

    /**
     * Actualiza un parqueadero existente.
     */
    public function update(ParkingLot $parkingLot, array $data): ParkingLot
    {
        return $this->repository->update($parkingLot, $data);
    }

    /**
     * Obtiene un parqueadero por su ID.
     */
    public function getById(int $id): ?ParkingLot
    {
        return $this->repository->find($id);
    }

    /**
     * Obtiene todos los parqueaderos de un usuario host.
     */
    public function getByUser(int $userId)
    {
        return $this->repository->getByUser($userId);
    }

    /**
     * Elimina un parqueadero existente.
     */
    public function delete(ParkingLot $parkingLot): bool
    {
        return $this->repository->delete($parkingLot);
    }
}
