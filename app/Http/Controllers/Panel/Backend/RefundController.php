<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Models\Reservation;
use App\Models\ReservationFlight;
use App\Models\ReservationHotel;

class RefundController extends Controller
{
    public function index()
    {

        $type = request('type', 'hotels');
        if ($type == 'hotels') {
            $reservations = Reservation::with('hotel')
                ->whereHas('hotel', function ($query) {
                    $query->where('status', 2)->where('refunded', 0);
                })
                ->filterDate()
                ->get();
        }
        if ($type == 'flights') {
            $reservations = [];
        }

        return view('admin.backend.refunds.index', compact('reservations', 'type'));
    }

    public function cancel_hotel($id)
    {
        $hotel = ReservationHotel::findOrFail($id);
        $hotel->status = 3;
        $hotel->refunded = 1;
        $hotel->save();

        return redirect()->back()->with('success', __('dashboard.reservation_canceled'));
    }

    public function cancel_flight($id)
    {
        $hotel = ReservationFlight::findOrFail($id);
        // $hotel->status = 3;
        // $hotel->refunded = 1;
        // $hotel->save();

        return redirect()->back()->with('success', __('dashboard.reservation_canceled'));
    }
}
