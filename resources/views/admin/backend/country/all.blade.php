@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_countries')" :url="'/admin-panel/country/create'">
        <li class="active"><a href="">{{ __('dashboard.view_countries') }}</a></li>
    </x-page-header>
    <x-panel-datatable :title="__('dashboard.view_countries')">
        <thead>
            <tr>
                <th id="sort_id">{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.country_name_ar') }}</th>
                <th>{{ __('dashboard.country_name_en') }}</th>
                <th class="text-center">{{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($countries as $country)
                <tr parent_id="{{ $country->id }}">
                    <td>{{ $country->id }}</td>
                    <td>{{ $country->name_ar }}</td>
                    <td>{{ $country->name_en }}</td>
                    <td class="text-center">
                        <ul class="icons-list">
                            @if (in_array('country-delete', $my_permissions))
                                <li class="text-danger-600">
                                    <a onclick="return false;" country_id="{{ $country->id }}"
                                        delete_url="/admin-panel/country/{{ $country->id }}" class="sweet_warning"
                                        href="#">
                                        <i class="icon-trash"></i>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('country-edit', $my_permissions))
                                <li class="text-primary-600">
                                    <a href="/admin-panel/country/{{ $country->id }}/edit">
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
