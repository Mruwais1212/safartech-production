@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_cities')" :url="'/admin-panel/city/create'">
        <li class="active"><a href="">{{ __('dashboard.view_cities') }}</a></li>
    </x-page-header>
    <x-panel-datatable :title="__('dashboard.view_cities')">
        <thead>
            <tr>
                <th id="sort_id">{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.city_name_ar') }}</th>
                <th>{{ __('dashboard.city_name_en') }}</th>
                <th>{{ __('dashboard.country') }}</th>
                <th class="text-center">{{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cities as $city)
                <tr parent_id="{{ $city->id }}">
                    <td>{{ $city->id }}</td>
                    <td>{{ $city->name_ar }}</td>
                    <td>{{ $city->name_en }}</td>
                    <td>{{ app()->getLocale() == 'ar' ? @$city->country->name_ar : @$city->country->name_en }}</td>
                    <td class="text-center">
                        <ul class="icons-list">
                            @if (in_array('city-delete', $my_permissions))
                                <li class="text-danger-600">
                                    <a onclick="return false;" city_id="{{ $city->id }}"
                                        delete_url="/admin-panel/city/{{ $city->id }}" class="sweet_warning"
                                        href="#">
                                        <i class="icon-trash"></i>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('city-edit', $my_permissions))
                                <li class="text-primary-600">
                                    <a href="/admin-panel/city/{{ $city->id }}/edit">
                                        <i class="icon-pencil7"></i>
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
