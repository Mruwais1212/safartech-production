@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
@endsection
@section('content')
    <x-page-header :header="isset($country)
        ? __('dashboard.edit') . ' ' . __('dashboard.country')
        : __('dashboard.add') . ' ' . __('dashboard.country')" :url="'/admin-panel/country'">
        <li><a href="/admin-panel/country">{{ __('dashboard.view_countries') }}</a></li>
        <li class="active">
            {{ isset($country) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.country') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($country)
        ? __('dashboard.edit') . ' ' . __('dashboard.country')
        : __('dashboard.add') . ' ' . __('dashboard.country')" :url="isset($country) ? '/admin-panel/country/' . $country->id : '/admin-panel/country'" :object="isset($country) ? $country : null">
        <x-input key='name_ar' :object="isset($country) ? $country : null" :placeholder="__('dashboard.country_name_ar')" type='text'></x-input>

        <x-input key='name_en' :object="isset($country) ? $country : null" :placeholder="__('dashboard.country_name_en')" type='text'></x-input>

        <x-input-file key='file' :object="null" placeholder="upload" type="file" prefix="/upload"> </x-input-file>
        </fieldset>
    </x-panel-form>
@endsection
