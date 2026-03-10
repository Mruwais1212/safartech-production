<?php

namespace App\Http\Controllers\Website;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Make sure this is imported

class MyTripController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        $reservations = Reservation::with('hotel', 'flight')->where('user_id', auth('web')->id())->where('payment_method', '!=', 0)->orderBy('id','desc')->get();

        return view('website.my-trips', compact('reservations'));
    }

    public function show($id)
    {
        $reservation = Reservation::with([
            'hotel', 
            'flights', 
            'passengers', 
            'flights.segments.origin', 
            'flights.segments.destination', 
            'flights.segments.airline',
            'user',
            'trip'
        ])
            ->where('user_id', auth('web')->id())
            ->where('payment_method', '!=', 0)
            ->where('id', $id)
            ->first();



        if (! $reservation) {
            return redirect()->back()->with('error', 'This Reservation Does not exist');
        }

        return view('website.my-trip-details', compact('reservation'));
    }
    public function flightTickets($id)
    {
        $reservation = Reservation::with(['passengers', 'flights'])->find($id);
        if(!$reservation || $reservation->user_id != auth('web')->id()){
            return redirect()->back()->with('error', 'This Reservation Does not exist');
        }
//        $htmlContent= view('flight-tickets',compact('reservation'))->render();
//return view('exports.flight-tickets',[
//    'reservation' => $reservation
//]);
        $pdf = Pdf::loadView('exports.flight-tickets', [
            'reservation' => $reservation
        ]);

        return $pdf->download('exported-file.pdf');


    }

    public function exportTripDetails($id)
    {
        $reservation = Reservation::with([
            'passengers',
            'hotel',
            'flights',
            'flights.segments',
            'flights.segments.origin',
            'flights.segments.destination',
            'flights.segments.airline',
            'trip'
        ])
        ->where('user_id', auth('web')->id())
        ->where('payment_method', '!=', 0)
        ->where('id', $id)
        ->first();

        if (!$reservation) {
            return redirect()->back()->with('error', 'This Reservation Does not exist');
        }

        $pdf = Pdf::loadView('trip-details', compact('reservation'));
        
        return $pdf->download('trip-details-' . $reservation->id . '.pdf');
    }
}
