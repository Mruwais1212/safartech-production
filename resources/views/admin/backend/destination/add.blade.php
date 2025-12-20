@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.addjs')
<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
@endsection
@section('content')
    <x-page-header :header="isset($destination)
        ? __('dashboard.edit') . ' ' . __('dashboard.destination')
        : __('dashboard.add') . ' ' . __('dashboard.destination')" :url="'/admin-panel/destination'">
        <li><a href="/admin-panel/destination">{{ __('dashboard.view_destinations') }}</a></li>
        <li class="active">
            {{ isset($destination) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.destination') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($destination)
        ? __('dashboard.edit') . ' ' . __('dashboard.destination')
        : __('dashboard.add') . ' ' . __('dashboard.destination')" :url="isset($destination) ? '/admin-panel/destination/' . $destination->id : '/admin-panel/destination'" :object="isset($destination) ? $destination : null">
        <x-textarea key='description_ar' :object="isset($destination) ? $destination : null" :placeholder="__('dashboard.description_ar')" class="form-control summernote"></x-textarea>
        <x-textarea key='description_en' :object="isset($destination) ? $destination : null" :placeholder="__('dashboard.description_en')" class="form-control summernote"></x-textarea>

        <x-select key='country_id' :placeholder="__('dashboard.select_country')">
            <option value="">{{ __('dashboard.select_country') }}</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ isset($destination) && $destination->country_id == $country->id
                        ? 'selected'
                        : (old('country_id') == $country->id
                            ? 'selected'
                            : '') }}>
                    {{ $country->name_ar }}</option>
            @endforeach
        </x-select>
        <x-select key='city_id' :placeholder="__('dashboard.select_city')">
            <option value="">{{ __('dashboard.select_city') }}</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}"
                    {{ isset($destination) && $destination->city_id == $city->id
                        ? 'selected'
                        : (old('city_id') == $city->id
                            ? 'selected'
                            : '') }}>
                    {{ $city->name_ar }}</option>
            @endforeach
        </x-select>
        <x-input key='estimated_cost' :object="isset($destination) ? $destination : null" :placeholder="__('dashboard.estimated_cost')" type='number'></x-input>
        <x-select key='travel_interests[]' :placeholder="__('dashboard.select_travel_interests')" multiple="true">
            <option value="">{{ __('dashboard.select_travel_interests') }}</option>
            @foreach ($travelInterests as $travelInterest)
                <option value="{{ $travelInterest->id }}"
                    {{ isset($destination) && $destination->travelInterests()->pluck('travel_interest_id')->contains($travelInterest->id)
                        ? 'selected'
                        : (in_array($travelInterest->id, old('travel_interests') ?? [])? 'selected': '') }}>
                    {{ $travelInterest->name_ar }}</option>
            @endforeach
        </x-select>

        <x-input key="latitude" :object="isset($destination) ? $destination : null" :placeholder="__('dashboard.latitude')" type="number"></x-input>
        <x-input key="longitude" :object="isset($destination) ? $destination : null" :placeholder="__('dashboard.longitude')" type="number"></x-input>

        </fieldset>
    </x-panel-form>
@endsection
