@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.site_activity')">
    <li class="active"><a href="">{{ __('dashboard.site_activity') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.site_activity')">
    <thead>
        <tr>
            <th>{{ __('dashboard.id') }}</th>
            <th>{{ __('dashboard.operation_time') }}</th>
            <th>{{ __('dashboard.title') }}</th>
            <th>{{ __('dashboard.message') }}</th>
            <th>{{ __('dashboard.data_changed') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activities as $activity)
        <tr parent_id="{{ $activity->id }}">
            <td>{{ $activity->id }}</td>
            <td>{{ @$activity->created_at->translatedFormat('Y-m-d h:i A') }}</td>
            <td>{{ app()->getLocale()=='ar' ? @$activity->title_ar:@$activity->title_en }}</td>
            <td style="direction: {{ __('dashboard.dir') }}">{{ app()->getLocale()=='ar' ?
                @$activity->message_ar:@$activity->message_en}}</td>
            <td>
                @php
                $prefix= Str::snake((array_reverse(explode('\\',$activity->activity_type))[0]));
                @endphp
                @if ($activity->data)
                <x-activity-details :activity="$activity" :prefix="$prefix"></x-activity-details>
                @endif
            </td>
        </tr>
        @endforeach

    </tbody>
</x-panel-datatable>
@endsection