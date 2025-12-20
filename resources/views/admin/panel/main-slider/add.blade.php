@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.addjs')
@endsection
@section('content')
<x-page-header
    :header=" isset($main_slider) ? __('dashboard.edit') .' '. __('dashboard.main_slider') : __('dashboard.add') .' '. __('dashboard.main_slider') "
    :url="'/admin-panel/main-slider'">
    <li><a href="/admin-panel/main-slider">{{ __('dashboard.view_main_sliders') }}</a></li>
    <li class="active">
        {{ isset($main_slider) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.main_slider') }}
    </li>
</x-page-header>
<x-panel-form
    :title=" isset($main_slider) ? __('dashboard.edit')  .' '. __('dashboard.main_slider'): __('dashboard.add') .' '.__('dashboard.main_slider') "
    :url="isset($main_slider) ? '/admin-panel/main-slider/' . $main_slider->id : '/admin-panel/main-slider'"
    :object="isset($main_slider) ?$main_slider:null">
    <x-input key='name_ar' :object="isset($main_slider)?$main_slider:null"
        :placeholder="__('dashboard.main_slider_name_ar')" type='text'></x-input>

    <x-input key='name_en' :object="isset($main_slider)?$main_slider:null"
        :placeholder="__('dashboard.main_slider_name_en')" type='text'></x-input>
    </fieldset>
</x-panel-form>
@endsection