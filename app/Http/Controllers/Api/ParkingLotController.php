<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ParkingLotService;
use App\Models\ParkingLot;
use Illuminate\Http\Request;

class ParkingLotController extends Controller
{
    protected ParkingLotService $service;

    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(ParkingLotService $service)
    {
        $this->service = $service;
    }

    /**
     * Crea un parqueadero para el usuario host autenticado.
     */
    public function store(Request $request)
    {
        // Validación de los datos del parqueadero
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            // Capacidad y disponibilidad
            'total_spots' => 'required|integer|min:1',

            // Tipos de vehículos permitidos
            'accepts_cars' => 'required|boolean',
            'accepts_motorcycles' => 'required|boolean',

            // Tarifas por tipo de vehículo
            'car_hourly_rate' => 'nullable|numeric|min:0',
            'motorcycle_hourly_rate' => 'nullable|numeric|min:0',

            // Horario general
            'schedule' => 'nullable|string|max:255',
        ]);

        // El usuario host autenticado es el propietario
        $validated['user_id'] = $request->user()->id;

        // La disponibilidad inicial es igual a los cupos totales
        $validated['available_spots'] = $validated['total_spots'];

        // Crear el parqueadero a través del servicio
        $parkingLot = $this->service->create($validated);

        return response()->json([
            'message' => 'Parqueadero creado correctamente.',
            'data' => $parkingLot,
        ], 201);
    }

    /**
     * Obtiene todos los parqueaderos del host autenticado.
     */
    public function index(Request $request)
    {
        $lots = $this->service->getByUser($request->user()->id);

        return response()->json([
            'data' => $lots,
        ]);
    }

    /**
     * Actualiza un parqueadero existente.
     */
    public function update(Request $request, ParkingLot $parkingLot)
    {
        // Se valida que el parqueadero pertenezca al host autenticado
        if ($parkingLot->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Validación de campos actualizables
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',

            'total_spots' => 'sometimes|integer|min:1',
            'available_spots' => 'sometimes|integer|min:0',

            // Tipos de vehículos permitidos
            'accepts_cars' => 'sometimes|boolean',
            'accepts_motorcycles' => 'sometimes|boolean',

            // Tarifas
            'car_hourly_rate' => 'sometimes|numeric|min:0',
            'motorcycle_hourly_rate' => 'sometimes|numeric|min:0',

            'schedule' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ]);

        // Actualización del parqueadero usando el servicio
        $updatedLot = $this->service->update($parkingLot, $validated);

        return response()->json([
            'message' => 'Parqueadero actualizado correctamente.',
            'data' => $updatedLot,
        ]);
    }

    /**
     * Elimina un parqueadero existente.
     */
    public function destroy(Request $request, ParkingLot $parkingLot)
    {
        // Validar propiedad del parqueadero
        if ($parkingLot->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $this->service->delete($parkingLot);

        return response()->json(['message' => 'Parqueadero eliminado.']);
    }
}
