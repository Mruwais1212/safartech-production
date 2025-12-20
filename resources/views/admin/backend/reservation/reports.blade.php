@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection
@section('content')
    <x-page-header :header="__('dashboard.money_reports')">
        <li class="active"><a href="">{{ __('dashboard.money_reports') }}</a></li>
    </x-page-header>

    <div class="text-center">
        @if ($from && $to)
            <p>{{ $from }} : {{ $to }}</p>
        @endif
        <form action="{{ url('admin-panel/financial_reports') }}" method="GET">
            <input type="hidden" name="export" value="1">
            <button type="submit" class="btn btn-primary">{{ __('dashboard.excel_money_reports') }}</button>
        </form>
    </div>

    <div class="content">
        <div class="panel panel-flat">
            <div class="panel-body">
                <form method="get" class="form-horizontal" action="{{ url('admin-panel/financial_reports') }}">
                    <x-input key="from" :object="isset(request()->from) ? request()->from : null" :placeholder="__('dashboard.from')" type="date"></x-input>
                    <x-input key="to" :object="isset(request()->to) ? request()->to : null" :placeholder="__('dashboard.to')" type="date"></x-input>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-panel-datatable :title="__('dashboard.money_reports')">
        <thead>
            <tr>
                <th id="sort_id">{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.date_time') }}</th>
                <th>{{ __('dashboard.user_name') }}</th>
                <th>{{ __('dashboard.email') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.destination') }}</th>
                <th>{{ __('dashboard.booking_reference_id') }}</th>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.reservation_price') }}</th>
                <th>{{ __('dashboard.flight_perecentage') }}</th>
                <th>{{ __('dashboard.hotel_perecentage') }}</th>
                <th>{{ __('dashboard.management_money') }}</th>
                <th>{{ __('dashboard.bill_no') }}</th>
                <th>{{ __('dashboard.Sales Amount') }}</th>
                <th>{{ __('dashboard.Tax') }}</th>
                <th>{{ __('dashboard.Total including tax') }}</th>
                <th>{{ __('dashboard.Payment Type') }}</th>
                <th>{{ __('dashboard.Payment Status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr parent_id="{{ $reservation->id }}">
                    <td>{{ $reservation->id }}</td>
                    <td>{{ $reservation->created_at }}</td>
                    <td>{{ $reservation->user->name }}</td>
                    <td>{{ $reservation->user->email }}</td>
                    <td>{{ $reservation->user->phone_code . $reservation->user->phone }}</td>
                    <td>{{ '------' }}</td>
                    <td>{{ $reservation->booking_reference_id ?? '---' }}</td>
                    <td>{{ $reservation->payment_id }}</td>
                    <td>{{ $reservation->price }}</td>
                    <td>{{ @$reservation->flight ? @$reservation->flight[0]->total_price : 0 }}</td>
                    <td>{{ @$reservation->hotel ? @$reservation->hotel->price : 0 }}</td>
                    <td>{{ '0' }}</td>
                    <td>{{ $reservation->payment_id }}</td>
                    <td>{{ '0' }}</td>
                    <td>{{ '0' }}</td>
                    <td>{{ $reservation->price }}</td>
                    <td>{{ __('dashboard.' . \App\Enums\PaymentMethod::getLabel($reservation->payment_method)) }}</td>
                    <td>{{ $reservation->paid_at ? __('dashboard.payment_done') : __('dashboard.payment_not') }}</td>
                    <td>
                        <a href="/generate-invoice/{{ $reservation->id }}" target="_blank" class="btn btn-primary">
                            <i class="fa fa-file"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-panel-datatable>
@endsection
