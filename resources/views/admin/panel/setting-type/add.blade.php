@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.addjs')
@endsection
@section('content')
<x-page-header
    :header=" isset($setting_type) ? __('dashboard.edit') : __('dashboard.add') . __('dashboard.view_setting_types') "
    :url="'/admin-panel/setting-type'">
    <li>
        <a href="/admin-panel/setting-type">{{ __('dashboard.view_setting_types') }}</a>
    </li>
    <li class="active">
        {{ isset($setting_type) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.setting_type') }}
    </li>
</x-page-header>
<x-panel-form
    :title=" isset($setting_type) ? __('dashboard.edit') : __('dashboard.add') . __('dashboard.setting_type') "
    :url="isset($setting_type) ? '/admin-panel/setting-type/' . $setting_type->id : '/admin-panel/setting-type'"
    :object="isset($setting_type) ?$setting_type:null">
    <x-input key='name_ar' :object="isset($setting_type)?$setting_type:null"
        :placeholder="__('dashboard.setting_type_name_ar')" type='text'></x-input>

    <x-input key='name_en' :object="isset($setting_type)?$setting_type:null"
        :placeholder="__('dashboard.setting_type_name_en')" type='text'></x-input>
</x-panel-form>
@endsection