@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.view_settings')" :url="'/admin-panel/setting/create'">
    <li class="active"><a href="">{{ __('dashboard.view_settings') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.view_settings')">
    <thead>
        <tr>
            <th>{{ __('dashboard.id') }} </th>
            <th>{{ __('dashboard.setting_name') }}</th>
            <th>{{ __('dashboard.setting_type') }}</th>
            <th>{{ __('dashboard.status') }}</th>
            <th>{{ __('dashboard.action_taken') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($settings as $setting)
        <tr parent_id="{{ $setting->id }}">
            <td>{{ $setting->id }}</td>
            <td>{{ app()->getLocale() == 'ar' ? $setting->name_ar :$setting->name_en }}</td>
            <td>{{ app()->getLocale() == 'ar' ?@$setting->settingType->name_ar :@$setting->settingType->name_en }}</td>
            <td>{{ $setting->status==1?__('dashboard.active'): __('dashboard.inactive') }}</td>
            <td align="center" class="center">
                <ul class="icons-list">
                    @if (in_array('setting-delete', $my_permissions))
                    <li class="text-danger-600">
                        <a onclick="return false;" setting_id="{{ $setting->id }}"
                            delete_url="/admin-panel/setting/{{ $setting->id }}" class="sweet_warning" href="#"><i
                                class="icon-trash"></i></a>
                    </li>
                    @endif
                    @if (in_array('setting-edit', $my_permissions))
                    <li class="text-primary-600">
                        <a href="/admin-panel/setting/{{ $setting->id }}/edit"><i class="icon-pencil7"></i></a>
                    </li>
                    @endif
                </ul>
            </td>
        </tr>
        @endforeach

    </tbody>
</x-panel-datatable>
@endsection