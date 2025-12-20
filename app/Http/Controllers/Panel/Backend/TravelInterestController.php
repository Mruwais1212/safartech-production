<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Backend\TravelInterestRequest;
use App\Models\TravelInterest;

class TravelInterestController extends Controller
{
    public function index()
    {
        $travelInterests = TravelInterest::all();

        return view('admin.backend.travel-interest.all', compact('travelInterests'));
    }

    public function create()
    {
        return view('admin.backend.travel-interest.add');
    }

    public function store(TravelInterestRequest $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
        }

        TravelInterest::create(['image' => @$imageName] + $request->validated());

        return back()->with('success', __('dashboard.travel_interest_created_successfully'));
    }

    public function edit($id)
    {
        $travelInterest = TravelInterest::find($id);

        return view('admin.backend.travel-interest.add', compact('travelInterest'));
    }

    public function update(TravelInterestRequest $request, $id)
    {
        $travelInterest = TravelInterest::find($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
        }

        $travelInterest->update(['image' => @$imageName ?? $travelInterest->image] + $request->validated());

        return back()->with('success', __('dashboard.travel_interest_updated_successfully'));
    }

    public function destroy($id)
    {
        $travelInterest = TravelInterest::find($id);

        if (! $travelInterest) {
            return back()->with('error', __('dashboard.travel_interest_not_found'));
        }

        $travelInterest->delete();

        return back()->with('success', __('dashboard.travel_interest_deleted_successfully'));
    }
}
