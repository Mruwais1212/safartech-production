@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_balances')">
        <li class="active"><a href="">{{ __('dashboard.view_balances') }}</a></li>
    </x-page-header>
    <x-panel-datatable :title="__('dashboard.view_balances')">
        <thead>
            <tr>
                <th id="sort_id">{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.user_name') }}</th>
                <th>{{ __('dashboard.balance_title') }}</th>
                <th>{{ __('dashboard.balance_description') }}</th>
                <th>{{ __('dashboard.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($balances as $balance)
                <tr parent_id="{{ $balance->id }}">
                    <td>{{ $balance->id }}</td>
                    <td>{{ $balance->user->name }}</td>
                    <td>{{ app()->getLocale() == 'ar' ? @$balance->title_ar : @$balance->title_ar }}</td>
                    <td>{{ app()->getLocale() == 'ar' ? @$balance->description_ar : @$balance->description_en }}</td>
                    <td>{{ $balance->amount }} {{ __('dashboard.sar') }}</td>
                </tr>
            @endforeach
        </tbody>
    </x-panel-datatable>
@endsection
