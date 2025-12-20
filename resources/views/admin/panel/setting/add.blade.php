@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.addjs')
<script type="text/javascript" src="/assets/js/admin/cbFamily.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
            $("h3 input:checkbox").cbFamily(function() {
                return $(this).parents("h3").next().find("input:checkbox");
            });
        });
</script>
@endsection
@section('content')
<x-page-header
    :header=" isset($setting) ? __('dashboard.edit') .' '. __('dashboard.settings'): __('dashboard.add') .' '. __('dashboard.settings') "
    :url="'/admin-panel/setting'">
    <li>
        <a href="/admin-panel/setting">{{ __('dashboard.view_settings') }}</a>
    </li>
    <li class="active">
        {{ isset($setting) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.setting') }}
    </li>
</x-page-header>
<x-panel-form
    :title=" isset($setting) ? __('dashboard.edit').' '. __('dashboard.setting') : __('dashboard.add') .' '. __('dashboard.setting') "
    :url="isset($setting) ?'/admin-panel/setting/' . $setting->id : '/admin-panel/setting'"
    :object="isset($setting) ?$setting:null">

    <x-input key='name_ar' :object="isset($setting)?$setting:null" :placeholder="__('dashboard.setting_name_ar')"
        type='text'></x-input>

    <x-input key='name_en' :object="isset($setting)?$setting:null" :placeholder="__('dashboard.setting_name_en')"
        type='text'></x-input>

    <x-input key='code' :object="isset($setting)?$setting:null" :placeholder="__('dashboard.setting_code')" type='text'>
    </x-input>

    <x-input key='note_ar' :object="isset($setting)?$setting:null" :placeholder="__('dashboard.setting_note_ar')"
        type='text'>
    </x-input>

    <x-input key='note_en' :object="isset($setting)?$setting:null" :placeholder="__('dashboard.setting_note_en')"
        type='text'>
    </x-input>

    <x-select key='setting_type_id' :placeholder="__('dashboard.setting_type')">
        <option value="">{{ __('dashboard.choose_type_of_setting') }}</option>
        @foreach ($settingTypes as $settingType)
        <option value="{{ $settingType->id }}" {{ isset($setting) && $setting->
            setting_type_id== $settingType->id ? 'selected' : (old('setting_type_id') ==
            $settingType->id? 'selected' : '') }}>
            {{ app()->getLocale() == 'ar' ? $settingType->name_ar:$settingType->name_en }}</option>
        @endforeach
    </x-select>

    <x-select key='input_type' :placeholder="__('dashboard.setting_input_type')">
        <option value="">{{ __('dashboard.choose_input_type_of_setting') }}</option>
        @foreach (['text','file','color','radio','number','textarea','url','tag','email'] as $input_type)
        <option value="{{ $input_type }}" {{ isset($setting) && $setting->input_type
            == $input_type ? 'selected' : (old('input_type') == $input_type ? 'selected' : '') }}>
            {{ ucfirst($input_type) }}
        </option>
        @endforeach
    </x-select>

    <x-input key='value' :object="isset($setting)?$setting:null" :placeholder="__('dashboard.setting_value')"
        type='text'></x-input>

    <x-input-radio :placeholder="__('dashboard.status')">
        <label class="radio-inline">
            <input type="radio" name="status" value="0" {{ isset($setting) ? ($setting->status==0?'checked':'') :
            old('status')==0?'checked':'' }}
            >
            {{ __('dashboard.inactive') }}
        </label>
        <label class="radio-inline">
            <input type="radio" name="status" value="1" {{ isset($setting) ? ($setting->status==1?'checked':'') :
            old('status')==1?'checked':'' }}
            >
            {{ __('dashboard.active') }}
        </label>
    </x-input-radio>
</x-panel-form>
<style>
    .radio-inline,
    .checkbox-inline {
        padding-right: 0px;
        margin-left: 75px;
    }
</style>
@endsection