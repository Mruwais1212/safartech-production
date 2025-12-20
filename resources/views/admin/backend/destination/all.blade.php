@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_destinations')" :url="'/admin-panel/destination/create'">
        <li class="active"><a href="">{{ __('dashboard.view_destinations') }}</a></li>
    </x-page-header>
    <x-panel-datatable :title="__('dashboard.view_destinations')">
        <thead>
            <tr>
                <th id="sort_id">{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.description_ar') }}</th>
                <th>{{ __('dashboard.description_en') }}</th>
                <th>{{ __('dashboard.country') }}</th>
                <th>{{ __('dashboard.city') }}</th>
                <th>{{ __('dashboard.estimated_cost') }}</th>
                <th class="text-center">{{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($destinations as $destination)
                <tr parent_id="{{ $destination->id }}">
                    <td>{{ $destination->id }}</td>
                    <td>{!! $destination->description_ar !!}</td>
                    <td>{!! $destination->description_en !!}</td>
                    <td>{{ app()->getLocale() == 'ar' ? @$destination->country->name_ar : @$destination->country->name_en }}
                    </td>
                    <td>{{ app()->getLocale() == 'ar' ? @$destination->city->name_ar : @$destination->city->name_en }}
                    </td>
                    <td>{{ $destination->estimated_cost }}</td>
                    <td class="text-center">
                        <ul class="icons-list">
                            @if (in_array('destination-delete', $my_permissions))
                                <li class="text-danger-600">
                                    <a onclick="return false;" destination_id="{{ $destination->id }}"
                                        delete_url="/admin-panel/destination/{{ $destination->id }}" class="sweet_warning"
                                        href="#">
                                        <i class="icon-trash"></i>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('destination-edit', $my_permissions))
                                <li class="text-primary-600">
                                    <a href="/admin-panel/destination/{{ $destination->id }}/edit">
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
