@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_group_notifications')" :url="'/admin-panel/group-notification/create'">
        <li class="active">
            <a href="/admin-panel/group-notification">{{ __('dashboard.view_group_notifications') }}</a>
        </li>
    </x-page-header>
    <x-panel-datatable :title="__('dashboard.group_notifications')">
        <thead>
            <tr>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.notification_title') }}</th>
                <th>{{ __('dashboard.notification_message') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($group_notifications as $group_notification)
                <tr>
                    <td>{{ $group_notification->id }}</td>
                    <td>{{ app()->getLocale() == 'ar' ? $group_notification->title_ar : $group_notification->title_en }}
                    </td>
                    <td>{{ app()->getLocale() == 'ar' ? $group_notification->message_ar : $group_notification->message_en }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-panel-datatable>
@endsection
