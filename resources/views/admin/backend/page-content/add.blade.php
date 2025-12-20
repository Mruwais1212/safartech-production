@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.addjs')
<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
@endsection
@section('content')
<x-page-header
    :header=" isset($page_content) ? __('dashboard.edit') .' '.  __('dashboard.page_content'): __('dashboard.add')  .' '.  __('dashboard.page_content') "
    :url="'/admin-panel/page-content'">
    <li>
        <a href="/admin-panel/page-content">{{ __('dashboard.view_page_contents') }}</a>
    </li>
    <li class="active">
        {{ isset($page_content) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.page_content') }}
    </li>
</x-page-header>
<div style=" text-align:center;margin: 14px;">
    <ul class="nav nav-pills nav-fill" role="tablist">
        <li class="nav-item active">
            <a data-toggle="tab" href="#arabic" class="nav-link">{{ __('dashboard.ar_lang') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#english">{{ __('dashboard.en_lang') }}</a>
        </li>
    </ul>
</div>
<x-panel-form
    :title=" isset($page_content) ? __('dashboard.edit')  .' '.  __('dashboard.page_content'): __('dashboard.add')  .' '.  __('dashboard.page_content') "
    :url="isset($page_content) ? '/admin-panel/page-content/' . $page_content->id : '/admin-panel/page-content'"
    :object="isset($page_content) ?$page_content:null">
    <div class="tab-content">
        <div id="arabic" class="tab-pane fade in active ">
            <x-input key="name_ar" :placeholder="__('dashboard.page_content_name_ar')" type="text"
                :object="isset($page_content) ? $page_content:null">></x-input>
            <x-textarea key="content_ar" :placeholder="__('dashboard.page_content_content_ar')"
                :object="isset($page_content) ? $page_content:null" class="summernote"></x-textarea>
        </div>
        <div id="english" class="tab-pane fade ">
            <x-input key="name_en" :placeholder="__('dashboard.page_content_name_en')" type="text"
                :object="isset($page_content) ? $page_content:null">></x-input>
            <x-textarea key="content_en" :placeholder="__('dashboard.page_content_content_en')"
                :object="isset($page_content) ? $page_content:null" class="summernote"></x-textarea>
        </div>
        <x-input key="slug" :placeholder="__('dashboard.page_content_slug')" type="text"
            :object="isset($page_content) ? $page_content:null"></x-input>

        <x-input key="url" :placeholder="__('dashboard.page_content_url')" type="text"
            :object="isset($page_content) ? $page_content:null"></x-input>
    </div>
</x-panel-form>
@endsection
