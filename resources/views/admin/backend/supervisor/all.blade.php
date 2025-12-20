@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_supervisors')" :url="'/admin-panel/supervisor/create'">
        <li class="active">
            <a href="/admin-panel/supervisor">{{ __('dashboard.view_supervisors') }}</a>
        </li>
    </x-page-header>
    <x-panel-datatable :title="__('dashboard.supervisors')">
        <thead>
            <tr>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.supervisor_name') }}</th>
                <th>{{ __('dashboard.email') }}</th>
                <th>{{ __('dashboard.supervisor_type') }}</th>
                <th>{{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supervisors as $supervisor)
                <tr>
                    <td>{{ $supervisor->id }}</td>
                    <td>{{ $supervisor->name }}</td>
                    <td>{{ $supervisor->email }}</td>
                    <td>{{ app()->getLocale() == 'ar' ? @$supervisor->privilegeGroup->name_ar : @$supervisor->privilegeGroup->name_en }}</td>
                    <td align="center" class="center">
                        <ul class="icons-list">
                            @if (in_array('supervisor-delete', $my_permissions))
                                <li class="text-danger-600">
                                    <a onclick="return false;" supervisor_id="{{ $supervisor->id }}"
                                        delete_url="/admin-panel/supervisor/{{ $supervisor->id }}" class="sweet_warning"
                                        href="#"><i class="icon-trash"></i>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('supervisor-edit', $my_permissions))
                                <li class="text-primary-600">
                                    <a href="/admin-panel/supervisor/{{ $supervisor->id }}/edit">
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
