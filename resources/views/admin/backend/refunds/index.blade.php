@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')

    <x-page-header :header="__('dashboard.view_refunds')">
        <li class="active"><a href="">{{ __('dashboard.view_refunds') }}</a></li>
    </x-page-header>
    <div class="col-md-12 text-center">
        <a href="{{ url()->current().'?type=hotels' }}" class="btn {{ $type == 'hotels' ? 'btn-warning' : 'btn-primary'}}"> {{ __('dashboard.hotels') }}</a>
        <a href="{{ url()->current().'?type=flights' }}" class="btn {{ $type == 'flights' ? 'btn-warning' : 'btn-primary'}}"> {{ __('dashboard.flights') }}</a>
    </div>
    <br>
    <br>

    <x-panel-datatable :title="__('dashboard.view_refunds')">
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
                       @include('admin.backend.refunds.reservation_details')
                       @include('admin.backend.refunds.refund_details',['type'=>$type])
                       <form method="POST" action="{{ $type=="hotels" ? url('admin-panel/cancel-hotel-reservations/'.$reservation->hotel->id) : url('admin-panel/cancel-flight-reservations/'.$reservation->flight->id) }}" class="d-inline">
                           @csrf
                           <button type="submit" class="btn btn-danger">{{ __('dashboard.refund') }}</button>
                       </form>
                   </td>
                </tr>
            @endforeach
        </tbody>
    </x-panel-datatable>
@endsection
