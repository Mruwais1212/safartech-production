@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.view_sliders')"
    url="/admin-panel/slider/create">
    <li class="active"><a href="/admin-panel/slider">{{ __('dashboard.view_sliders') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.view_sliders')">
    <thead>
        <tr>
            <th id="sort_id">{{ __('dashboard.id') }}</th>
            <th>{{ __('dashboard.slider_title') }}</th>
            <th>{{ __('dashboard.slider_image') }}</th>
            <th class="text-center">{{ __('dashboard.action_taken') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sliders as $slider)
        <tr parent_id="{{ $slider->id }}">
            <td>{{ $slider->id }}</td>
            <td>{{ app()->getLocale() == 'ar' ? $slider->title_ar : $slider->title_en }}</td>
            <td>
                @if ($slider->photo)
                <img width="50" height="50" src="/uploads/{{ $slider->photo }}">
                @endif
            </td>
            <td align="center" class="center">
                <ul class="icons-list">
                    @if (in_array('sliders-edit', $my_permissions))
                    <li class="text-primary-600">
                        <a href="/admin-panel/slider/{{ $slider->id }}/edit"><i class="icon-pencil7"></i></a>
                    </li>
                    @endif
                    @if (in_array('sliders-delete', $my_permissions))
                    <li class="text-danger-600">
                        <a onclick="return false;" slider_id="{{ $slider->id }}"
                            delete_url="/admin-panel/slider/{{ $slider->id }}" class="sweet_warning" href="#">
                            <i class="icon-trash"></i>
                        </a>
                    </li>
                    @endif
                </ul>
            </td>
        </tr>
        @endforeach
    </tbody>
</x-panel-datatable>
@endsection