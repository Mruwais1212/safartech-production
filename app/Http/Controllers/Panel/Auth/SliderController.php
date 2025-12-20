<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\SliderRequest;
use App\Models\Panel\MainSlider;
use App\Models\Panel\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        // $mainSlider = MainSlider::find(request()->main_slider);

        // if (!$mainSlider) {
        //     return back()->with('error', __('dashboard.main_slider_not_found'));
        // }

        $sliders = Slider::where('main_slider_id', 1)->get();

        return view('admin.panel.slider.all', compact('sliders'));
    }

    public function create()
    {
        // $mainSliders = MainSlider::all();
        // $mainSlider  = MainSlider::find(request()->main_slider);

        // if (!$mainSlider) {
        //     return back()->with('error', __('dashboard.main_slider_not_found'));
        // }

        return view('admin.panel.slider.add');
    }

    public function store(SliderRequest $request)
    {
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $name = 'slider-'.time().'.'.$photo->getClientOriginalExtension();
            $photo->move('uploads', $name);
        }

        Slider::create(['photo' => @$name ?: null] + $request->validated());

        return back()->with('success', __('dashboard.slider_created_successfully'));
    }

    public function edit($id)
    {
        // $mainSliders = MainSlider::all();
        $slider = Slider::find($id);

        if (! $slider) {
            return back()->with('error', __('dashboard.slider_not_found'));
        }

        // $mainSlider = MainSlider::find($slider->main_slider_id);

        // if (!$mainSlider) {
        //     return back()->with('error', __('dashboard.main_slider_not_found'));
        // }

        return view('admin.panel.slider.add', compact('slider'));
    }

    public function update(SliderRequest $request, Slider $slider)
    {
        if ($request->hasFile('photo')) {
            if (is_file('uploads/'.$slider->photo)) {
                unlink('uploads/'.$slider->photo);
            }
            $photo = $request->file('photo');
            $name = 'slider-'.time().'.'.$photo->getClientOriginalExtension();
            $photo->move('uploads', $name);
        }

        $slider->update(['photo' => @$name ?: $slider->photo] + $request->validated());

        return back()->with('success', __('dashboard.slider_updated_successfully'));
    }

    public function destroy($id)
    {
        $slider = Slider::find($id);

        if (! $slider) {
            return back()->with('error', __('dashboard.slider_not_found'));
        }

        $slider->delete();

        return back()->with('success', __('dashboard.slider_deleted_successfully'));
    }
}
