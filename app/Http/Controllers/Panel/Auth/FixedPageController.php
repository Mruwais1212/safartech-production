<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Auth\FixedPageRequest;
use App\Models\Panel\FixedPage;

class FixedPageController extends Controller
{
    public function index()
    {
        $fixed_pages = FixedPage::all();

        return view('admin.panel.fixed-page.all', compact('fixed_pages'));
    }

    public function create()
    {
        return view('admin.panel.fixed-page.add');
    }

    public function store(FixedPageRequest $request)
    {
        FixedPage::create($request->validated());

        return back()->with('success', __('dashboard.fixed_page_created_successfully'));
    }

    public function edit($id)
    {
        $fixed_page = FixedPage::find($id);

        return view('admin.panel.fixed-page.add', compact('fixed_page'));
    }

    public function update(FixedPageRequest $request, $id)
    {
        $fixed_page = FixedPage::find($id);
        $fixed_page->update($request->all());

        return back()->with('success', __('dashboard.fixed_page_updated_successfully'));
    }

    public function destroy($id)
    {
        $fixed_page = FixedPage::find($id);

        if (! $fixed_page) {
            return back()->with('error', __('dashboard.fixed_page_not_found'));
        }

        $fixed_page->delete();

        return back()->with('success', __('dashboard.fixed_page_deleted_successfully'));
    }
}
