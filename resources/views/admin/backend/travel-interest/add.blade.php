@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
@endsection
@section('content')
    <x-page-header :header="isset($travelInterest)
        ? __('dashboard.edit') . ' ' . __('dashboard.travel_interest')
        : __('dashboard.add') . ' ' . __('dashboard.travel_interest')" :url="'/admin-panel/travel-interest'">
        <li><a href="/admin-panel/travel-interest">{{ __('dashboard.view_travel_interests') }}</a></li>
        <li class="active">
            {{ isset($travelInterest) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.travel_interest') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($travelInterest)
        ? __('dashboard.edit') . ' ' . __('dashboard.travel_interest')
        : __('dashboard.add') . ' ' . __('dashboard.travel_interest')" :url="isset($travelInterest)
        ? '/admin-panel/travel-interest/' . $travelInterest->id
        : '/admin-panel/travel-interest'" :object="isset($travelInterest) ? $travelInterest : null">
        <x-input key='name_ar' :object="isset($travelInterest) ? $travelInterest : null" :placeholder="__('dashboard.travel_interest_name_ar')" type='text'></x-input>

        <x-input key='name_en' :object="isset($travelInterest) ? $travelInterest : null" :placeholder="__('dashboard.travel_interest_name_en')" type='text'></x-input>

        <x-select key='type' :placeholder="__('dashboard.travel_interest_type')">
            <option value="">{{ __('dashboard.travel_interest_type') }}</option>
                <option value="ai"
                    {{ isset($travelInterest) && $travelInterest->type == 'ai'
                        ? 'selected'
                        : (old('type') == 'ai'
                            ? 'selected'
                            : '') }}>
                    {{ __('dashboard.ai') }}</option>
                    <option value="dalelah"
                    {{ isset($travelInterest) && $travelInterest->type == 'dalelah'
                        ? 'selected'
                        : (old('type') == 'dalelah'
                            ? 'selected'
                            : '') }}>
                    {{ __('dashboard.dalelah') }}</option>
        </x-select>
        <x-input-file key='image' :object="isset($travelInterest) ? $travelInterest : null" :placeholder="__('dashboard.image')" prefix='/uploads'></x-input-file>
    </x-panel-form>
@endsection
