@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')

    <x-page-header :header="__('dashboard.view_reservations')">
        <li class="active"><a href="">{{ __('dashboard.view_reservations') }}</a></li>
    </x-page-header>
    @if(url()->current() != url('admin-panel/reservations') && url()->current() != url('admin-panel/hotel-and-flight-reservations'))
    <div class="col-md-12 text-center">
        <a href="{{ url()->current() }}" class="btn {{ !request()->status ? 'btn-warning' : 'btn-primary'}}"> {{ __('dashboard.view_all') }}</a>
        <a href="{{ url()->current().'?status=canceled' }}" class="btn {{ request()->status == 'canceled' ? 'btn-warning' : 'btn-primary'}}"> {{ __('dashboard.cancel_done') }}</a>
    </div>
    <br>
    <br>
    @endif

    <x-panel-datatable :title="__('dashboard.view_reservations')">
        <thead>
            <tr>
                <th id="sort_id">{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.user_name') }}</th>
                <th>{{ __('dashboard.reservation_type') }}</th>
                <th>{{ __('dashboard.reservation_price') }}</th>
                <th>{{ __('dashboard.reservation_currency') }}</th>
                <th class="text-center">{{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr parent_id="{{ $reservation->id }}">
                   <td>{{ $reservation->id }}</td>
                   <td>{{ $reservation->user->name }}</td>
                   <td>{{ $reservation->getType() }}</td>
                   <td>{{ $reservation->price }}</td>
                   <td>{{ $reservation->currency }}</td>
                   <td class="text-center">
                       @include('admin.backend.reservation.reservation_details')
                   </td>
                </tr>
            @endforeach
        </tbody>
    </x-panel-datatable>
@endsection
