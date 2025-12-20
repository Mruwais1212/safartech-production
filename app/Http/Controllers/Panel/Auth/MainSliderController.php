<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\MainSliderRequest;
use App\Models\Panel\MainSlider;

class MainSliderController extends Controller
{
    public function index()
    {
        $main_sliders = MainSlider::all();

        return view('admin.panel.main-slider.all', compact('main_sliders'));
    }

    public function create()
    {
        return view('admin.panel.main-slider.add');
    }

    public function store(MainSliderRequest $request)
    {
        MainSlider::create($request->validated());

        return back()->with('success', __('dashboard.main_slider_created_successfully'));
    }

    public function edit($id)
    {
        $main_slider = MainSlider::find($id);

        return view('admin.panel.main-slider.add', compact('main_slider'));
    }

    public function update(MainSliderRequest $request, $id)
    {
        $main_slider = MainSlider::find($id);
        $main_slider->update($request->validated());

        return back()->with('success', __('dashboard.main_slider_updated_successfully'));
    }

    public function destroy($id)
    {
        $main_slider = MainSlider::find($id);

        if (! $main_slider) {
            return back()->with('error', __('dashboard.main_slider_not_found'));
        }

        $main_slider->delete();

        return back()->with('success', __('dashboard.main_slider_deleted_successfully'));
    }
}
