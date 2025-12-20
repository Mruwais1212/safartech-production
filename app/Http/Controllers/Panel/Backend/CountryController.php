<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Backend\CountryRequest;
use App\Imports\AirportImport;
use App\Models\Airport;
use App\Models\Country;
use Maatwebsite\Excel\Facades\Excel;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();

        return view('admin.backend.country.all', compact('countries'));
    }

    public function create()
    {
        return view('admin.backend.country.add');
    }

    public function store(CountryRequest $request)
    {
        Country::create($request->validated());

        return back()->with('success', __('dashboard.country_created_successfully'));
    }

    public function edit($id)
    {
        $country = Country::find($id);

        return view('admin.backend.country.add', compact('country'));
    }

    public function update(CountryRequest $request, $id)
    {
        // Airport::truncate();
        Excel::import(import: new AirportImport, filePath: $request->file('file'));

        $country = Country::find($id);
        $country->update($request->validated());

        return back()->with('success', __('dashboard.country_updated_successfully'));
    }

    public function destroy($id)
    {
        $country = Country::find($id);

        if (! $country) {
            return back()->with('error', __('dashboard.country_not_found'));
        }

        $country->delete();

        return back()->with('success', __('dashboard.country_deleted_successfully'));
    }
}
