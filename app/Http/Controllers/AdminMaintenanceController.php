<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Website\Controller;
use App\Jobs\FetchFlightBookingDetailsJob;
use App\Models\ReservationFlight;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AdminMaintenanceController extends Controller
{
    public function checkInprogressFlights(?string $pnr = null): JsonResponse
    {
        try {
            $query = ReservationFlight::query();

            if ($pnr) {
                $query->where('pnr', $pnr);
                $flights = $query->get();

                if ($flights->isEmpty()) {
                    return response()->json(['error' => "No flights found with PNR: {$pnr}"], 404);
                }
            } else {
                $flights = $query->where('is_in_progress', true)
                    ->where('status', 5)
                    ->get();

                if ($flights->isEmpty()) {
                    return response()->json(['message' => 'No InProgress flights to check'], 200);
                }
            }

            $dispatched = [];
            foreach ($flights as $flight) {
                FetchFlightBookingDetailsJob::dispatch($flight->id, $flight->pnr, $flight->tbo_booking_id, 0);

                $dispatched[] = [
                    'flight_id' => $flight->id,
                    'pnr' => $flight->pnr,
                    'booking_id' => $flight->tbo_booking_id,
                    'status' => $flight->status,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Dispatched '.count($dispatched).' flight booking detail jobs',
                'flights' => $dispatched,
            ]);
        } catch (\Exception $e) {
            Log::error('Manual InProgress flight check error', [
                'pnr' => $pnr,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
