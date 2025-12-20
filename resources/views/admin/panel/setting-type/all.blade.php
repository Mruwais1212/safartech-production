@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.view_setting_types')" :url="'/admin-panel/setting-type/create'">
    <li class="active"><a href="">{{ __('dashboard.view_setting_types') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.view_setting_types')">
    <thead>
        <tr>
            <th>{{ __('dashboard.id') }} </th>
            <th>{{ __('dashboard.setting_type_name_ar') }}</th>
            <th>{{ __('dashboard.setting_type_name_en') }}</th>
            <th>{{ __('dashboard.action_taken') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($setting_types as $setting_type)
        <tr parent_id="{{ $setting_type->id }}">
            <td>{{ $setting_type->id }}</td>
            <td>{{ $setting_type->name_ar }}</td>
            <td>{{ $setting_type->name_en }}</td>
            <td align="center" class="center">
                <ul class="icons-list">
                    @if (in_array('setting-types-delete', $my_permissions))
                    <li class="text-danger-600">
                        <a onclick="return false;" setting_type_id="{{ $setting_type->id }}"
                            delete_url="/admin-panel/setting-type/{{ $setting_type->id }}" class="sweet_warning"
                            href="#"><i class="icon-trash"></i>
                        </a>
                    </li>
                    @endif
                    @if (in_array('setting-types-edit', $my_permissions))
                    <li class="text-primary-600">
                        <a href="/admin-panel/setting-type/{{ $setting_type->id }}/edit"><i class="icon-pencil7"></i>
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