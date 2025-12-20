<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Http\Requests\Panel\Backend\PageContentRequest;
use App\Models\PageContent;

class PageContentController extends Controller
{
    public function index()
    {
        $page_contents = PageContent::all();

        return view('admin.backend.page-content.all', compact('page_contents'));
    }

    public function create()
    {
        return view('admin.backend.page-content.add');
    }

    public function store(PageContentRequest $request)
    {
        PageContent::create($request->validated());

        return back()->with('success', __('dashboard.page_content_created_successfully'));
    }

    public function edit($id)
    {
        $page_content = PageContent::find($id);

        return view('admin.backend.page-content.add', compact('page_content'));
    }

    public function update(PageContentRequest $request, $id)
    {
        $page_content = PageContent::find($id);
        $page_content->update($request->all());

        return back()->with('success', __('dashboard.page_content_updated_successfully'));
    }

    public function destroy($id)
    {
        $page_content = PageContent::find($id);

        if (! $page_content) {
            return back()->with('error', __('dashboard.page_content_not_found'));
        }

        $page_content->delete();

        return back()->with('success', __('dashboard.page_content_deleted_successfully'));
    }
}
