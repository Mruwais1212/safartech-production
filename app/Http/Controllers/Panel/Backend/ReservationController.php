<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Models\Reservation;
use App\Exports\ReservationsExport;
use App\Models\ReservationFlight;
use App\Models\ReservationHotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('hotel', 'flight')
            ->filterDate()
            ->latest()
            ->paginate(50);
        return view('admin.backend.reservation.index', compact('reservations'));
    }

    /**
     * Permanently delete ALL reservation data.
     *
     * Requires an explicit confirmation token supplied as POST body:
     *   confirm_token = "DELETE_ALL_RESERVATIONS"
     *
     * This prevents accidental or CSRF-driven destruction of production data.
     * The route is already protected by auth:admin + permission middleware.
     */
    public function delete_all(Request $request)
    {
        $requiredToken = 'DELETE_ALL_RESERVATIONS';

        if ($request->input('confirm_token') !== $requiredToken) {
            Log::warning('delete_all attempted without correct confirmation token', [
                'admin_id' => auth('admin')->id(),
                'ip'       => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Missing or incorrect confirm_token. Send confirm_token=DELETE_ALL_RESERVATIONS to proceed.',
            ], 422);
        }

        Log::warning('delete_all executed by admin', [
            'admin_id' => auth('admin')->id(),
            'ip'       => $request->ip(),
        ]);

        DB::table('reservation_flights')->truncate();
        DB::table('reservation_hotels')->truncate();
        DB::table('reservation_passengers')->truncate();
        DB::table('flight_segments')->truncate();
        DB::table('flight_segment_airlines')->truncate();
        DB::table('flight_segment_destinations')->truncate();
        DB::table('flight_segment_origins')->truncate();
        DB::table('reservations')->truncate();

        return response()->json(['message' => 'All reservation data deleted.']);
    }

    public function financial_reports()
    {
        $from = request('from', null);
        $to = request('to', null);
        $reservations = Reservation::with('hotel', 'flight')
            ->where('payment_method', '>=', 1)
            ->when($from && $to, function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from, $to]);
            })
            ->get();
        if (request('export')) {
            return Excel::download(new ReservationsExport($reservations), 'reservations_report.xlsx');
        }
        return view('admin.backend.reservation.reports', compact('reservations', 'from', 'to'));
    }

    public function hotel()
    {
        $type = request('status', null);
        if ($type == 'pending') {
            $reservations = Reservation::with('hotel')
                ->whereHas('hotel', function ($query) {
                    $query->where('status', 2)->where('refunded', 0);
                })
                ->filterDate()
                ->get();
        }
        if ($type == 'canceled') {
            $reservations = Reservation::with('hotel')
                ->whereHas('hotel', function ($query) {
                    $query->where('status', 3)->where('refunded', 1);
                })
                ->filterDate()
                ->get();
        }
        if ($type != 'canceled' && $type != 'pending') {
            $reservations = Reservation::with('hotel')
                ->where('payment_method', '>=', 1)
                ->filterDate()
                ->whereHas('hotel')
                ->get();
        }
        return view('admin.backend.reservation.index', compact('reservations'));
    }

    public function flight()
    {
        $type = request('status', null);
        if ($type == 'pending') {
            $reservations = Reservation::with('flight')
                ->whereHas('flight', function ($query) {
                    $query->where('status', 2)->where('refunded', 0);
                })
                ->filterDate()
                ->get();
        }
        if ($type == 'canceled') {
            $reservations = Reservation::with('flight')
                ->whereHas('flight', function ($query) {
                    $query->where('status', 3)->where('refunded', 1);
                })
                ->filterDate()
                ->get();
        }
        if ($type != 'canceled' && $type != 'pending') {
            $reservations = Reservation::with('flight')
                ->where('payment_method', '>=', 1)
                ->filterDate()
                ->whereHas('hotel')
                ->get();
        }

        return view('admin.backend.reservation.index', compact('reservations'));
    }

    public function hotelAndFlight()
    {
        $reservations = Reservation::with('hotel', 'flight')
            ->where('payment_method', '>=', 1)
            ->filterDate()
            ->whereHas('hotel')
            ->whereHas('flight')
            ->get();

        return view('admin.backend.reservation.index', compact('reservations'));
    }

    public function cancel_hotel($id)
    {
        $hotel = ReservationHotel::findOrFail($id);
        $hotel->status = 3;
        $hotel->refunded = 1;
        $hotel->save();

        return redirect()->back()->with('success', __('dashboard.refund_done'));
    }

    public function cancel_flight($id)
    {
        $hotel = ReservationFlight::findOrFail($id);
        // $hotel->status = 3;
        // $hotel->refunded = 1;
        // $hotel->save();

        return redirect()->back()->with('success', __('dashboard.refund_done'));
    }
}
