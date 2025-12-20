<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Requests\Panel\Backend\AiPlannerContentRequest;
use App\Models\Panel\AiPlannerSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Panel\Controller;
use App\Models\Panel\Setting;

class AiPlannerContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = AiPlannerSection::orderBy('sort','desc')->get();
        $settings = Setting::where('status', 1)->where('setting_type_id',5)->get();
        return view('admin.panel.ai-page.all', compact('items','settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.panel.ai-page.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AiPlannerContentRequest $request)
    {
        AiPlannerSection::create($request->validated());

        return redirect(url('admin-panel/ai-planner-sections'))->with('success', __('dashboard.page_content_created_successfully'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = AiPlannerSection::find($id);

        return view('admin.panel.ai-page.add', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AiPlannerContentRequest $request, string $id)
    {
        $item = AiPlannerSection::find($id);
        $item->update($request->all());

        return redirect(url('admin-panel/ai-planner-sections'))->with('success', __('dashboard.page_content_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = AiPlannerSection::findOrFail($id);
        $item->delete();

        return back()->with('success', __('dashboard.page_content_deleted_successfully'));
    }
}
