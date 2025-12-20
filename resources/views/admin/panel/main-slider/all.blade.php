@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.view_main_sliders')" :url="'/admin-panel/main-slider/create'">
    <li class="active"><a href="">{{ __('dashboard.view_main_sliders') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.view_main_sliders')">
    <thead>
        <tr>
            <th id="sort_id">{{ __('dashboard.id') }}</th>
            <th>{{ __('dashboard.slider_title') }}</th>
            <th>{{ __('dashboard.view_sliders') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($main_sliders as $main_slider)
        <tr parent_id="{{ $main_slider->id }}">
            <td>{{ $main_slider->id }}</td>
            <td>
                <a href="/admin-panel/slider?main_slider={{ $main_slider->id }}">
                    {{ __('dashboard.view') }} {{ $main_slider->name_ar }}
                </a>
            </td>
            <td>
                <a href="/admin-panel/slider?main_slider={{ $main_slider->id }}">
                    {{ __('dashboard.view') }}
                    {{ __('dashboard.count') }} {{ '(' . $main_slider->sliders->count() . ')' }}
                    {{ __('dashboard.slider') }}
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</x-panel-datatable>
@endsection