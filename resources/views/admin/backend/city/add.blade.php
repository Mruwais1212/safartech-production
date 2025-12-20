@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
@endsection
@section('content')
    <x-page-header :header="isset($city)
        ? __('dashboard.edit') . ' ' . __('dashboard.city')
        : __('dashboard.add') . ' ' . __('dashboard.city')" :url="'/admin-panel/city'">
        <li><a href="/admin-panel/city">{{ __('dashboard.view_cities') }}</a></li>
        <li class="active">
            {{ isset($city) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.city') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($city)
        ? __('dashboard.edit') . ' ' . __('dashboard.city')
        : __('dashboard.add') . ' ' . __('dashboard.city')" :url="isset($city) ? '/admin-panel/city/' . $city->id : '/admin-panel/city'" :object="isset($city) ? $city : null">
        <x-input key='name_ar' :object="isset($city) ? $city : null" :placeholder="__('dashboard.city_name_ar')" type='text'></x-input>

        <x-input key='name_en' :object="isset($city) ? $city : null" :placeholder="__('dashboard.city_name_en')" type='text'></x-input>

        <x-select key='country_id' :placeholder="__('dashboard.select_country')">
            <option value="">{{ __('dashboard.select_country') }}</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ isset($city) && $city->country_id == $country->id
                        ? 'selected'
                        : (old('country_id') == $country->id
                            ? 'selected'
                            : '') }}>
                    {{ $country->name_ar }}</option>
            @endforeach
        </x-select>

        </fieldset>
    </x-panel-form>
@endsection
