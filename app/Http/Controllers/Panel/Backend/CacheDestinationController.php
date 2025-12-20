<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Models\Country;
use App\Models\TravelInterest;
use App\Services\CacheDestinationService;
use Illuminate\Http\Request;

class CacheDestinationController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        $travelInterests = TravelInterest::all();

        return view('admin.backend.destination.cache', compact('countries', 'travelInterests'));
    }

    public function store(Request $request)
    {
        return CacheDestinationService::getDestinations($request);
    }
}
