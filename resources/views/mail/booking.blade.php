@php $lang=\Illuminate\Support\Facades\App::getLocale()@endphp

<!DOCTYPE html>
@if ($lang == 'ar')
    <html lang="ar" dir="rtl">
@else
    <html>
@endif

<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title></title>
    <style type="text/css">
        @import url(//fonts.googleapis.com/earlyaccess/droidarabickufi.css);
    </style>
    <style>
        @if ($lang == 'ar')
            .content {
                direction: rtl;
            }
        @endif
    </style>
</head>

<body>
    <div class="content">
        <div class="container px-5 last-step mt-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="plan-section">
                                <div class="header-summery justify-content-start">
                                    <h3 class="text-dark">
                                        @if (isset($reservation->type) && in_array($reservation->type, [1]))
                                            {{ __('dashboard.flight and hotel trip') }}
                                            {{ __('dashboard.from') }}
                                            {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                            ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                            {{ __('dashboard.to') }}
                                            {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                            ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                        @endif

                                        @if (isset($reservation->type) && in_array($reservation->type, [2]))
                                            {{ __('dashboard.hotel booking only') }}
                                            {{ __('dashboard.to') }}
                                            {{ @$reservation->hotel->hotel_name }}
                                        @endif

                                        @if (isset($reservation->type) && in_array($reservation->type, [3]))
                                            {{ __('dashboard.flight only trip') }}
                                            {{ __('dashboard.from') }}
                                            {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                            ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                            {{ __('dashboard.to') }}
                                            {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                            ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                        @endif

                                        <small class="print-title"> - {{ __('dashboard.reservation number') }}
                                            {{ $reservation->id }}</small>
                                    </h3>
                                </div>
                                <hr>
                                <div class="plan-details">
                                    <div class="plan-details-title">
                                        <h6>{{ __('dashboard.Traveling details') }}</h6>
                                    </div>
                                    <p class="details">
                                        @if (isset($reservation->type) && in_array($reservation->type, [1]))
                                            {{ __('dashboard.this is a flight and hotel trip') }}
                                            {{ __('dashboard.from') }}
                                            {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                            ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                            {{ __('dashboard.to') }}
                                            {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                            ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                        @endif

                                        @if (isset($reservation->type) && in_array($reservation->type, [2]))
                                            {{ __('dashboard.this is hotel booking only') }}
                                            {{ __('dashboard.to') }}
                                            {{ @$reservation->hotel->hotel_name }}
                                        @endif

                                        @if (isset($reservation->type) && in_array($reservation->type, [3]))
                                            {{ __('dashboard.this is a flight booking only') }}
                                            {{ __('dashboard.from') }}
                                            {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                            ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                            {{ __('dashboard.to') }}
                                            {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                            ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                        @endif
                                    </p>
                                </div>
                                <hr>
                                {{-- <div class="plan-details">
                                <div class="plan-details-title">
                                    <h6>Advices for this trip</h6>
                                </div>
                                <ul class="details ps-3">
                                    <li>
                                        Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                        tellus dui
                                        purus
                                    </li>
                                    <li>
                                        Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                        tellus dui
                                        purus
                                    </li>
                                    <li>
                                        Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                        tellus dui
                                        purus
                                    </li>
                                    <li>
                                        Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                        tellus dui
                                        purus
                                    </li>
                                </ul>
                            </div> --}}
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="plan-section">

                                <div class="header-summery">
                                    <h3 class="text-dark">{{ __('dashboard.Passenger Details') }}</h3>
                                </div>

                                <hr>
                                <div class="plan-details">
                                    <div class="plan-details-title">
                                        <h6>{{ __('dashboard.Traveling details') }}</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center passenger-table ">
                                            <thead>
                                                <tr>
                                                    <th class="text-warning" scope="col">
                                                        {{ __('dashboard.Name') }}</th>
                                                    <th class="text-warning" scope="col">
                                                        {{ __('dashboard.Passport') }}</th>
                                                    <th class="text-warning" scope="col">
                                                        {{ __('dashboard.Phone Number') }}</th>
                                                    <th class="text-warning" scope="col">
                                                        {{ __('dashboard.Gender') }}</th>
                                                    <th class="text-warning" scope="col">
                                                        {{ __('dashboard.Age') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($reservation->passengers as $passenger)
                                                    <tr>
                                                        <td>{{ @$passenger->first_name }}
                                                            {{ @$passenger->last_name }}
                                                        </td>
                                                        <td>{{ @$passenger->passport_number }}</td>
                                                        <td>{{ @$passenger->phone }}</td>
                                                        <td>
                                                            {{ @$passenger->gender == 1 ? 'Male' : 'Female' }}
                                                        </td>
                                                        @php
                                                            $dob = new DateTime(@$passenger->birth_date);
                                                            $today = new DateTime('today');
                                                            $year = $dob->diff($today)->y;
                                                            $month = $dob->diff($today)->m;
                                                            $day = $dob->diff($today)->d;
                                                        @endphp

                                                        <td>{{ $year . ' سنة  و  ' . $month . '  شهر   ' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($reservation->type == 1 || $reservation->type == 2)
                            <div class="col-md-12 mt-5">
                                <div class="plan-section">
                                    <div class="header-summery">
                                        <h3 class="text-dark">{{ __('dashboard.Hotel Details') }}</h3>
                                    </div>
                                    <div class="plan-details hotel-detailss">
                                        <div class="hotel-details-form">
                                            <div class="gallery-container position-relative">
                                                <div style="opacity: 0" class="top-title">
                                                    <h3>{{ $reservation->hotel->hotel_name }}</h3>
                                                    <p>{{ $reservation->hotel->hotel_address }}</p>
                                                </div>
                                                <div style="opacity: 0" class="bottom-title">
                                                    <h3>{{ __('dashboard.Hotel Roles') }}</h3>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <p>
                                                            {{ __('dashboard.Check-In') }} :
                                                            {{ $reservation->hotel->check_in }}
                                                            {{ __('dashboard.Check-Out') }} :
                                                            {{ $reservation->hotel->check_out }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-center passenger-table ">
                                                <thead>
                                                    <tr>
                                                        <th class="text-warning" scope="col">
                                                            {{ $reservation->hotel->hotel_name }}
                                                        </th>
                                                        <th class="text-warning" scope="col">
                                                            {{ __('dashboard.Hotel Roles') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $reservation->hotel->hotel_address }}
                                                        </td>
                                                        <td>{{ __('dashboard.Check-In') }} :
                                                            {{ $reservation->hotel->check_in }}
                                                            {{ __('dashboard.Check-Out') }} :
                                                            {{ $reservation->hotel->check_out }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($reservation->type == 3 || $reservation->type == 1)
                            <div class="col-md-12 mt-5">
                                <div class="plan-section">
                                    <div class="header-summery">
                                        <h3 class="text-dark">{{ __('dashboard.Flight Details') }}</h3>
                                    </div>

                                    <div class="plan-details flight-details">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @foreach ($reservation->flight[0]->segments as $key => $flight)
                                                    <h3 class="text-dark mb-4">
                                                        {{ __('dashboard.Flight Type') }} : <span
                                                            class="text-warning">{{ $key == 0 ? __('dashboard.inboard') : __('dashboard.outboard') }}</span>
                                                    </h3>
                                                    <div class="airway-details">
                                                        <div class="flight-time">
                                                            <div class="time">
                                                                {{ $flight->origin->city_name }}
                                                                ({{ $flight->origin->country_name }})
                                                            </div>
                                                            <div class="time-code">
                                                                {{ \Carbon\Carbon::parse($flight->origin->arr_time)->format('d-m-Y H:i A') }}
                                                            </div>
                                                        </div>
                                                        <div class="flight-road">
                                                            <div class="road">
                                                                <span>{{ $flight->duration }}</span>
                                                                {{ __('dashboard.Min') }}</span>
                                                                <hr class="my-2">
                                                                <span>{{ $flight->airline->airline_name }}</span>
                                                            </div>
                                                            <i class="fas fa-plane"></i>
                                                        </div>
                                                        <div class="flight-time">
                                                            <div class="time">
                                                                {{ $flight->destination->city_name }}
                                                                ({{ $flight->destination->country_name }})
                                                            </div>
                                                            <div class="time-code">
                                                                {{ \Carbon\Carbon::parse($flight->destination->arr_time)->format('d-m-Y H:i A') }}
                                                            </div>
                                                        </div>
                                                        <div class="plan-details">
                                                            <div class="plan-details-title">
                                                                <h6 style="font-size: 12px;">
                                                                    {{ __('dashboard.Flight Class') }} :
                                                                    <span class="text-warning">
                                                                        @if ($flight->cabin_class == 2)
                                                                            {{ __('dashboard.Economy') }}
                                                                        @endif

                                                                        @if ($flight->cabin_class == 3)
                                                                            {{ __('dashboard.PremiumEconomy') }}
                                                                        @endif

                                                                        @if ($flight->cabin_class == 4)
                                                                            {{ __('dashboard.Business') }}
                                                                        @endif

                                                                        @if ($flight->cabin_class == 5)
                                                                            {{ __('dashboard.PremiumBusiness') }}
                                                                        @endif

                                                                        @if ($flight->cabin_class == 6)
                                                                            {{ __('dashboard.First Class') }}
                                                                        @endif

                                                                    </span>
                                                                </h6>
                                                            </div>
                                                            <div class="plan-details-title" style="font-size: 12px;">
                                                                <h6 style="font-size: 12px;">
                                                                    {{ __('dashboard.Bags') }} :
                                                                    <span class="text-warning"
                                                                        style="font-size: 12px;">{{ $flight->baggage }}</span>
                                                                </h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-12 mt-5">
                            <div class="plan-section">

                                <div class="header-summery">
                                    <h3 class="text-dark">{{ __('dashboard.Payment details') }}</h3>
                                </div>

                                <div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <ul class="summary-prices-list">
                                                @if (@$reservation->flight->count() > 0)
                                                    <li>
                                                        <span>{{ __('dashboard.Flight') }} : </span>
                                                        <span>{{ @$reservation->flight[0]->total_price }}
                                                            {{ __('dashboard.dollar') }}</span>
                                                    </li>
                                                @endif
                                                @if (@$reservation->hotel)
                                                    <li>
                                                        <span>{{ __('dashboard.Hotel') }} : </span>
                                                        <span>{{ @$reservation->hotel->price }}
                                                            {{ __('dashboard.' . $reservation->hotel->currency) }}</span>
                                                    </li>
                                                @endif
                                                <li class="total-price">
                                                    <span>{{ __('dashboard.Total') }} : </span>
                                                    <span>{{ $reservation->price }}
                                                        {{ __('dashboard.' . $reservation->currency) }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
