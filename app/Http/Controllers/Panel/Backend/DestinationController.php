<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Backend\DestinationRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Destination;
use App\Models\TravelInterest;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::with('city', 'country')->get();

        return view('admin.backend.destination.all', compact('destinations'));
    }

    public function create()
    {
        $travelInterests = TravelInterest::all();
        $countries = Country::all();
        $cities = City::all();

        return view('admin.backend.destination.add', compact('travelInterests', 'countries', 'cities'));
    }

    public function store(DestinationRequest $request)
    {
        Destination::create($request->validated());

        return back()->with('success', __('dashboard.destination_created_successfully'));
    }

    public function edit($id)
    {
        $destination = Destination::find($id);

        if (! $destination) {
            return back()->with('error', __('dashboard.destination_not_found'));
        }

        $travelInterests = TravelInterest::all();
        $countries = Country::all();
        $cities = City::all();

        return view('admin.backend.destination.add', compact('destination', 'travelInterests', 'countries', 'cities'));
    }

    public function update(DestinationRequest $request, $id)
    {
        $destination = Destination::find($id);

        if (! $destination) {
            return back()->with('error', __('dashboard.destination_not_found'));
        }

        $destination->update($request->validated());

        return back()->with('success', __('dashboard.destination_updated_successfully'));
    }

    public function destroy($id)
    {
        $destination = Destination::find($id);

        if (! $destination) {
            return back()->with('error', __('dashboard.destination_not_found'));
        }

        $destination->delete();

        return back()->with('success', __('dashboard.destination_deleted_successfully'));
    }
}
