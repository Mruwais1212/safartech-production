@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_travel_interests')" :url="'/admin-panel/travel-interest/create'">
        <li class="active"><a href="">{{ __('dashboard.view_travel_interests') }}</a></li>
    </x-page-header>
    <x-panel-datatable :title="__('dashboard.view_travel_interests')">
        <thead>
            <tr>
                <th id="sort_id">{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.travel_interest_name_ar') }}</th>
                <th>{{ __('dashboard.travel_interest_name_en') }}</th>
                <th>{{ __('dashboard.travel_interest_type') }}</th>
                <th>{{ __('dashboard.image') }}</th>
                <th class="text-center">{{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($travelInterests as $travelInterest)
                <tr parent_id="{{ $travelInterest->id }}">
                    <td>{{ $travelInterest->id }}</td>
                    <td>{{ $travelInterest->name_ar }}</td>
                    <td>{{ $travelInterest->name_en }}</td>
                    <td>{{ $travelInterest->type ? __('dashboard.'.$travelInterest->type) : '' }}</td>
                    <td>
                        @if (is_file('uploads/' . $travelInterest->image))
                            <img src="/uploads/{{ $travelInterest->image }}" height="50" alt=" {{ $travelInterest->name_ar }}">
                        @endif
                    </td>
                    <td class="text-center">
                        <ul class="icons-list">
                            @if (in_array('travel-interest-delete', $my_permissions))
                                <li class="text-danger-600">
                                    <a onclick="return false;" travel_interest_id="{{ $travelInterest->id }}"
                                        delete_url="/admin-panel/travel-interest/{{ $travelInterest->id }}"
                                        class="sweet_warning" href="#">
                                        <i class="icon-trash"></i>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('travel-interest-edit', $my_permissions))
                                <li class="text-primary-600">
                                    <a href="/admin-panel/travel-interest/{{ $travelInterest->id }}/edit">
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
