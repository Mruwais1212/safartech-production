@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.notifications')">
    <li class="active"><a href="">{{ __('dashboard.notifications') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.notifications')">
    <thead>
        <tr>
            <th>{{ __('dashboard.id') }}</th>
            <th>{{ __('dashboard.time_send') }}</th>
            <th>{{ __('dashboard.title') }}</th>
            <th>{{ __('dashboard.message') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($notifications as $notification)
        <tr parent_id="{{ $notification->id }}">
            <td>{{ $notification->id }}</td>
            <td>{{ @$notification->created_at->translatedFormat('Y-m-d h:i A') }}</td>
            <td>{{ app()->getLocale()=='ar' ? @$notification->title_ar:@$notification->title_en }}</td>
            <td style="direction: {{ __('dashboard.dir') }}">{{ app()->getLocale()=='ar' ?
                @$notification->message_ar:@$notification->message_en}}</td>
        </tr>
        @endforeach

    </tbody>
</x-panel-datatable>
@endsection