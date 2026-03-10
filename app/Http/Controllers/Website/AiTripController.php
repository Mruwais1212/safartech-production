<?php

namespace App\Http\Controllers\Website;

use App\Models\Country;
use App\Models\Destination;
use App\Models\TravelInterest;
use App\Services\CacheDestinationService;
use App\Services\SearchService;
use Illuminate\Http\Request;

class AiTripController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        $interests = TravelInterest::where('type','ai')->get();
        $countries = Country::all();

        if (session()->has('flight')) {
            session(['flight' => null]);
            session(['passengers' => null]);
            session(['traceId' => null]);
        }
        session(['searchParams' => null]);
        session(['searchResults' => null]);

        return view('website.ai-trip', compact('interests', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination' => ['nullable', 'string', 'max:120'],
            'destination_code' => ['nullable', 'string', 'max:10'],
            'trip_id' => ['nullable', 'integer'],
        ]);

        SearchService::searchNew($request, 1);

        return CacheDestinationService::getDestinations($request);
    }

    public function searchPlans(Request $request)
    {
        $request->validate([
            'trip_id' => ['required', 'integer', 'exists:destinations,id'],
        ]);

        SearchService::search($request, 1);

        $trip = Destination::find($request->trip_id);

        return redirect('/choose/' . $trip->id);
    }
    public function searchPlansNew(Request $request)
    {
        $request->validate([
            'trip_id' => ['required', 'integer', 'exists:destinations,id'],
        ]);

        SearchService::searchNew($request, 1);

        $trip = Destination::find($request->trip_id);

        return redirect('/choose/' . $trip->id);
    }

}
