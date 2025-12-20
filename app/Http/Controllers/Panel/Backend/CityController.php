<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Backend\CityRequest;
use App\Models\City;
use App\Models\Country;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::all();

        return view('admin.backend.city.all', compact('cities'));
    }

    public function create()
    {
        $countries = Country::all();

        return view('admin.backend.city.add', compact('countries'));
    }

    public function store(CityRequest $request)
    {
        City::create($request->validated());

        return back()->with('success', __('dashboard.city_created_successfully'));
    }

    public function edit($id)
    {
        $city = City::find($id);
        $countries = Country::all();

        return view('admin.backend.city.add', compact('city', 'countries'));
    }

    public function update(CityRequest $request, $id)
    {
        $city = City::find($id);
        $city->update($request->validated());

        return back()->with('success', __('dashboard.city_updated_successfully'));
    }

    public function destroy($id)
    {
        $city = City::find($id);

        if (! $city) {
            return back()->with('error', __('dashboard.city_not_found'));
        }

        $city->delete();

        return back()->with('success', __('dashboard.city_deleted_successfully'));
    }
}
