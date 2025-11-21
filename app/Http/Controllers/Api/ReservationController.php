<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected ReservationService $service;

    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(ReservationService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista todas las reservas del driver autenticado.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $reservations = $this->service->getReservationsForUser($user);

        return response()->json([
            'data' => $reservations,
        ]);
    }

    /**
     * Crea una nueva reserva para el driver autenticado.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Validar los datos de entrada
        $validated = $request->validate([
            'parking_lot_id' => 'required|integer|exists:parking_lots,id',
            'vehicle_type'   => 'required|in:car,motorcycle',
            'hours'          => 'required|integer|min:1',
        ]);

        try {
            $reservation = $this->service->createReservation($user, $validated);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Reserva creada correctamente.',
            'data'    => $reservation,
        ], 201);
    }

    /**
     * Cancela una reserva del driver autenticado.
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        try {
            $updated = $this->service->cancelReservation($reservation, $user);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Reserva cancelada correctamente.',
            'data'    => $updated,
        ]);
    }

    /**
     * Procesar el pago de una reserva.
     */
    public function pay(Request $request, Reservation $reservation)
    {
        // Validar acceso del driver
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        // Validación del método de pago
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,credit_card,debit_card,nequi,daviplata'
        ]);

        // Ejecutar el pago
        $paidReservation = $this->service->pay($reservation, $validated);

        return response()->json([
            'message' => 'Pago procesado correctamente.',
            'data' => $paidReservation
        ]);
    }
}
