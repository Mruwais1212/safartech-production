@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.addjs')
<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
@endsection
@section('content')
<x-page-header
    :header=" isset($fixed_page) ? __('dashboard.edit') .' '.  __('dashboard.fixed_page'): __('dashboard.add')  .' '.  __('dashboard.fixed_page') "
    :url="'/admin-panel/fixed-page'">
    <li>
        <a href="/admin-panel/fixed-page">{{ __('dashboard.view_fixed_pages') }}</a>
    </li>
    <li class="active">
        {{ isset($fixed_page) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.fixed_page') }}
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
    :title=" isset($fixed_page) ? __('dashboard.edit')  .' '.  __('dashboard.fixed_page'): __('dashboard.add')  .' '.  __('dashboard.fixed_page') "
    :url="isset($fixed_page) ? '/admin-panel/fixed-page/' . $fixed_page->id : '/admin-panel/fixed-page'"
    :object="isset($fixed_page) ?$fixed_page:null">
    <div class="tab-content">
        <div id="arabic" class="tab-pane fade in active ">
            <x-input key="name_ar" :placeholder="__('dashboard.fixed_page_name_ar')" type="text"
                :object="isset($fixed_page) ? $fixed_page:null">></x-input>
            <x-textarea key="content_ar" :placeholder="__('dashboard.fixed_page_content_ar')"
                :object="isset($fixed_page) ? $fixed_page:null" class="summernote"></x-textarea>
        </div>
        <div id="english" class="tab-pane fade ">
            <x-input key="name_en" :placeholder="__('dashboard.fixed_page_name_en')" type="text"
                :object="isset($fixed_page) ? $fixed_page:null">></x-input>
            <x-textarea key="content_en" :placeholder="__('dashboard.fixed_page_content_en')"
                :object="isset($fixed_page) ? $fixed_page:null" class="summernote"></x-textarea>
        </div>
        <x-input key="slug" :placeholder="__('dashboard.fixed_page_slug')" type="text"
            :object="isset($fixed_page) ? $fixed_page:null">></x-input>
    </div>
</x-panel-form>
@endsection