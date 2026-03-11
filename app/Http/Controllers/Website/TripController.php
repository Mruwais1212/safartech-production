<?php

namespace App\Http\Controllers\Website;

use App\Models\Destination;
use App\Models\Reservation;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        // $trips = Destination::inRandomOrder()->take(16)->get();

        // return view('website.trips', compact('trips'));
        $reservations = Reservation::with('hotel', 'flight')->where('user_id', auth('web')->id())->where('payment_method', '!=', 0)->get();

        return view('website.my-trips', compact('reservations'));
    }

    public function show($id)
    {
        $searchParams = session('searchParams');
        // return session()->all();
        $trip = Destination::find($id);

        if (! $trip) {
            return abort(404);
        }

        $relatedTrips = Destination::inRandomOrder()->where('id', '!=', $trip->id)->where('country_id', $trip->country_id)->take(3)->get();

        return view('website.trip', compact('trip', 'relatedTrips', 'searchParams'));
    }

    public function plan($id)
    {
        $trip = Destination::find($id);

        if (! $trip) {
            return abort(404);
        }

        $relatedTrips = Destination::inRandomOrder()->where('id', '!=', $trip->id)->where('country_id', $trip->country_id)->take(3)->get();

        return view('website.single-trip', compact('trip', 'relatedTrips'));
    }

    public function showResults()
    {
        $searchParams = session('searchParams');
        $searchResults = session('searchResults');

        if (! $searchParams || ! $searchResults) {
            return redirect()->route('ai-trip.index');
        }

        return view('website.search-results', compact('searchParams', 'searchResults'));
    }
}
