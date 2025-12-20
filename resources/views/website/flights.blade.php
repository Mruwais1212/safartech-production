@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/flights.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.Flights') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- search form -->
    <div class="container search-page position-relative">
        <!-- Filter overlay for mobile -->
        <div class="filter-overlay"></div>

        <div class="row">
            <div class="col-md-12">
                <form class="steps-form" method="GET" action="{{ url('/flights') }}">
                    <!-- Preserve essential search parameters -->
                    <input type="hidden" name="origin" value="{{ request()->origin }}">
                    <input type="hidden" name="destination" value="{{ request()->destination }}">
                    <input type="hidden" name="start_date" value="{{ request()->start_date }}">
                    <input type="hidden" name="end_date" value="{{ request()->end_date }}">
                    @if(request()->has('type'))
                        <input type="hidden" name="type" value="{{ request()->type }}">
                    @endif
                    @if(request()->has('adults'))
                        <input type="hidden" name="adults" value="{{ request()->adults }}">
                    @endif
                    @if(request()->has('children'))
                        <input type="hidden" name="children" value="{{ request()->children }}">
                    @endif
                    @if(request()->has('infants'))
                        <input type="hidden" name="infants" value="{{ request()->infants }}">
                    @endif
                    
                    <div class="container">
                        <div class="row">
                            <div class="search-top-bar">
                                <div class="flight-box flight-from">
                                    <i class="bi bi-geo-alt"></i>
                                    <div class="location-info">
                                        <p>{{ __('dashboard.Flying from') }}</p>
                                        <p>{{ isset(session('preferences')['origin_name']) ? session('preferences')['origin_name'] : '' }}
                                            ({{ request()->origin }}) </p>
                                    </div>
                                </div>
                                <div class="flight-arrow">
                                    <i class="bi bi-arrow-left-right text-warning"></i>
                                </div>
                                <div class="flight-box flight-to">
                                    <i class="bi bi-geo-alt"></i>
                                    <div class="location-info">
                                        <p>{{ __('dashboard.Flying to') }}</p>
                                        <p>{{ isset(session('preferences')['destination_name']) ? session('preferences')['destination_name'] : '' }}
                                            ( {{ request()->destination }})</p>
                                    </div>
                                </div>
                                <div class="flight-box date-from">
                                    <i class="bi bi-calendar4"></i>
                                    <div class="date-info">
                                        <p>{{ __('dashboard.Departing') }}</p>
                                        <p>{{ request()->start_date }}</p>
                                    </div>
                                </div>
                                @if(request()->start_date != request()->end_date && request()->journey_type != 1)
                                <div class="flight-box date-to">
                                    <i class="bi bi-calendar4"></i>
                                    <div class="date-info">
                                        <p>{{ __('dashboard.Returning') }}</p>
                                        <p>{{ request()->end_date }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="col-12 mt-5"></div>

                            <div class="col-md-3 pe-4 filter-sidebar">
                                <div class="remove-class">
                                    <a href="#" class="remove-class-link">
                                        <i class="fa fa-times text-warning fa-2x"></i>
                                    </a>
                                    <a href="#" id="resetbtn" class="clear-class-link">
                                        Clear
                                    </a>
                                </div>
                                <br>
                                <div class="search-sidebar">
                                    <h3 class="filter-title">{{ __('dashboard.Filter by') }}</h3>

                                    <div class="form-check form-switch p-0">
                                        <label class="form-check-label" for="direct_flight">
                                            <h6>{{ __('dashboard.direct_flight') }}</h6>
                                        </label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="direct_flight"
                                            name="direct" {{ request('direct') ? 'checked' : '' }}>
                                    </div>

                                    <div class="form-check form-switch p-0">
                                        <label class="form-check-label" for="one_stop">
                                            <h6>{{ __('dashboard.One Stop Flights') }}</h6>
                                        </label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="one_stop"
                                            name="one_stop" {{ request('one_stop') ? 'checked' : '' }}>
                                    </div>

                                    <div class="filter-card">
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle">{{ __('dashboard.Flight Type') }}</h3>
                                        </div>
                                        <ul class="filter-list">
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="journey_type" value="1"
                                                        class="form-check-input"
                                                        {{ request('journey_type') == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.One Way') }}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="journey_type" value="2"
                                                        class="form-check-input"
                                                        {{ request('journey_type') == 2 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.Return') }}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="journey_type" value="3"
                                                        class="form-check-input"
                                                        {{ request('journey_type') == 3 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.Multi Stop') }}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="journey_type" value="4"
                                                        class="form-check-input"
                                                        {{ request('journey_type') == 4 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.Advance Search') }}</label>
                                                </div>
                                            </li>

                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="journey_type" value="5"
                                                        class="form-check-input"
                                                        {{ request('journey_type') == 5 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.Special Return') }}</label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="filter-card">
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle">{{ __('dashboard.Flight Class') }}</h3>
                                        </div>
                                        <ul class="filter-list">
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="flight_class" value="1"
                                                        class="form-check-input"
                                                        {{ request('flight_class') == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.all') }}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="flight_class" value="2"
                                                        class="form-check-input"
                                                        {{ request('flight_class') == 2 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.Economy') }}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="flight_class" value="3"
                                                        class="form-check-input"
                                                        {{ request('flight_class') == 3 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.PremiumEconomy') }}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="flight_class" value="4"
                                                        class="form-check-input"
                                                        {{ request('flight_class') == 4 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.Business') }}</label>
                                                </div>
                                            </li>

                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="flight_class" value="5"
                                                        class="form-check-input"
                                                        {{ request('flight_class') == 5 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.PremiumBusiness') }}</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="form-check">
                                                    <input type="radio" name="flight_class" value="6"
                                                        class="form-check-input"
                                                        {{ request('flight_class') == 6 ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="exampleCheck1">{{ __('dashboard.FirstClass') }}</label>
                                                </div>
                                            </li>

                                        </ul>
                                    </div>

                                    <div class="filter-card">
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle">{{ __('dashboard.Departure time') }}
                                                {{ __('dashboard.in') }}
                                                {{ isset(session('preferences')['origin_name']) ? session('preferences')['origin_name'] : '' }}
                                            </h3>
                                        </div>
                                        <div class="grid">
                                            <label class="card">
                                                <input name="flight_time" class="radio" value="00:00:00"
                                                    {{ request('flight_time') == '00:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.all_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="flight_time" class="radio" value="08:00:00"
                                                    {{ request('flight_time') == '08:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <img style="object-fit: contain;" width="auto"
                                                        src="/site/img/filter1.png" alt="chose type" />
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.morning_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="flight_time" class="radio" value="14:00:00"
                                                    {{ request('flight_time') == '14:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <img style="object-fit: contain;" width="auto"
                                                        src="/site/img/filter2.png" alt="chose type" />
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.afternoon_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="flight_time" class="radio" value="19:00:00"
                                                    {{ request('flight_time') == '19:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <img style="object-fit: contain;" width="auto"
                                                        src="/site/img/filter3.png" alt="chose type" />
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.evening_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="flight_time" class="radio" value="01:00:00"
                                                    {{ request('flight_time') == '01:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <img style="object-fit: contain;" width="auto"
                                                        src="/site/img/filter4.png" alt="chose type" />
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.night_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="filter-card">
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle"> {{ __('dashboard.Arrival time') }}
                                                {{ __('dashboard.in') }}
                                                {{ isset(session('preferences')['destination_name']) ? session('preferences')['destination_name'] : '' }}
                                            </h3>
                                        </div>
                                        <div class="grid">
                                            <label class="card">
                                                <input name="flight_time_return" class="radio" value="00:00:00"
                                                    {{ request('flight_time_return') == '00:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.all_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="flight_time_return" class="radio" value="08:00:00"
                                                    {{ request('flight_time_return') == '08:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <img style="object-fit: contain;" width="auto"
                                                        src="/site/img/filter1.png" alt="chose type" />
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.morning_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="flight_time_return" class="radio" value="14:00:00"
                                                    {{ request('flight_time_return') == '14:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <img style="object-fit: contain;" width="auto"
                                                        src="/site/img/filter2.png" alt="chose type" />
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.afternoon_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="flight_time_return" class="radio" value="19:00:00"
                                                    {{ request('flight_time_return') == '19:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <img style="object-fit: contain;" width="auto"
                                                        src="/site/img/filter3.png" alt="chose type" />
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.evening_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="flight_time_return" class="radio" value="01:00:00"
                                                    {{ request('flight_time_return') == '01:00:00' ? 'checked' : '' }}
                                                    type="radio">
                                                <span class="plan-details">
                                                    <img style="object-fit: contain;" width="auto"
                                                        src="/site/img/filter4.png" alt="chose type" />
                                                    <div class="plan-title">
                                                        <h5>{{ __('dashboard.night_flight') }}</h5>
                                                    </div>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="btn btn-outline-warning py-3 w-100">{{ __('dashboard.Search') }}</button>
                                </div>
                            </div>

                            <div class="col-md-9  big-section ps-4">
                                {{-- {{dd(session()->all())}} --}}
                                <ul class="filter-path">
                                    <li class="bold">{{ __('dashboard.Choose flight') }}</li>
                                    <li class="bold">
                                        @if (session('flight.outbound_flight_selected'))
                                            <span
                                                style="padding-right: 7px;padding-left: 7px;">{{ '  ( ' . request()->origin . '->' . request()->destination . ' )  ' }}</span>
                                            <a href="/flights?type=1" class="">
                                                {{ __('dashboard.change_flight') }}</a>
                                        @else
                                            {{ __('dashboard.Choose departing flight') }}
                                        @endif
                                    </li>
                                    <li class="bold">
                                        @if (session('flight.inbound_flight'))
                                            @if (session('flight.inbound_flight_selected'))
                                                <span
                                                    style="padding-right: 7px;padding-left: 7px;">{{ '  ( ' . request()->destination . '->' . request()->origin . ' )  ' }}</span>
                                                <a href="/flights?type=2" class="">
                                                    {{ __('dashboard.change_flight') }}</a>
                                            @else
                                                {{ __('dashboard.Choose returning flight') }}
                                            @endif
                                        @endif
                                    </li>
                                </ul>
                                <button type="button"
                                    class="btn btn-warning py-2 w-100 filter-btn">{{ __('dashboard.Sort & Filter') }}</button>
                                <div class="sort-section">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="alert alert-warning text-white">
                                                {{ __('dashboard.Prices may change based on availability and are not final until you complete your purchase') }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mt-0">
                                                <label>{{ __('dashboard.sort') }} </label>
                                                <select class="form-control" name="sort" id="sortSelect" onchange="this.form.submit()">
                                                    <option value="price_from_low_to_high"
                                                        {{ request('sort') == 'price_from_low_to_high' ? 'selected' : '' }}>
                                                        {{ __('dashboard.price_from_low_to_high') }}</option>
                                                    <option value="price_from_high_to_low"
                                                        {{ request('sort') == 'price_from_high_to_low' ? 'selected' : '' }}>
                                                        {{ __('dashboard.price_from_high_to_low') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-results flight-results">
                                    <h3 class="title-results">
                                        {{ __('dashboard.Recommended departing flights') }}

                                    </h3>

                                    <div class="result-cards">
                                        @php
                                            $flights =
                                                session('flight.outbound_flight_selected') && request()->type != 1
                                                    ? $inbound_flights
                                                    : $outbound_flights;
                                        @endphp
                                        @forelse ($flights as $keyFlight=> $flight)
                                            <div class="result-card">
                                                <div class="btn btn-warning w-50 text-start rounded-0">
                                                    <a href="javascript:void(0)" id="{{ $keyFlight }}"
                                                        class="btn btn-danger selectType"
                                                        style="{{ session('flight.outbound_flight_selected') && request()->type != 1 ? 'display:none' : '' }}">
                                                        {{count($flight['Segments'])>1?__('dashboard.going return trip'): __('dashboard.going trip') }}
                                                    </a>
                                                    {{-- <span>
                                                       IsLCC: {{$flight['IsLCC']}}

                                                    </span> --}}
                                                    <a href="javascript:void(0)" id="{{ $keyFlight }}"
                                                        class="btn btn-primary selectType"
                                                        style="{{ !(session('flight.outbound_flight_selected') && request()->type != 1) ? 'display:none' : '' }}">
                                                        {{ __('dashboard.return trip') }}
                                                    </a>
                                                    <a class="btn btn-success float-start" data-bs-toggle="modal" data-bs-target="#flight_details_modal{{$keyFlight}}">{{ __('dashboard.Flight Details') }}</a>
                                                    <div class="modal fade" id="flight_details_modal{{$keyFlight}}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-auto-width" style="max-width: 100%;margin: 0;">
                                                            <div class="modal-content" style="width: 100%;margin: 0;">
                                                                <x-flight-details  :flightSegment="$flight['Segments']"></x-flight-details>
                                                                <!-- Modal content here -->
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                @foreach ($flight['Segments'] as $segmentKey => $segments)
                                                    <div class="card-section segment{{ $segmentKey }}_{{ $keyFlight }}"
                                                        style="{{ $segmentKey == 1 ? 'display:none' : 'display:block' }}">
                                                        <div class="result-card-title" style="direction: rtl;">
                                                            {{ __('dashboard.from') }} :
                                                            {{ \Carbon\Carbon::parse($segments[0]['Origin']['DepTime'])->format('Y-m-d h:i A') }}
                                                        </div>
                                                        <div class="result-card-title" style="direction: rtl;">
                                                            {{ __('dashboard.to') }} :
                                                            {{ \Carbon\Carbon::parse(@$segments[count($flight['Segments'][0]) - 1]['Destination']['ArrTime'])->format('Y-m-d h:i A') }}
                                                        </div>
                                                    </div>
                                                    <div class="card-section segment{{ $segmentKey }}_{{ $keyFlight }}"
                                                        style="width: 50%; {{ $segmentKey == 1 ? 'display:none' : 'display:block' }}">
                                                        <div class="result-card-sub">
                                                            <div class="airway-details" style="flex-wrap: nowrap;">

                                                                @foreach (@$segments as $key => $segment)
                                                                    <div class="flight-time">
                                                                        <div class="time">
                                                                            {{ \Carbon\Carbon::parse($segment['Origin']['DepTime'])->format('h:i A') }}
                                                                        </div>
                                                                        <div class="time-code">
                                                                            {{ $segment['Origin']['Airport']['CityName'] }}
                                                                            ({{ $segment['Origin']['Airport']['CityCode'] }})
                                                                        </div>
                                                                    </div>
                                                                    <div class="flight-road">
                                                                        <div class="road-time">
                                                                            <span>{{ $segment['Duration'] }}
                                                                                {{ __('dashboard.Min') }}</span>
                                                                            <hr class="my-2">
                                                                            <span>{{ $segment['Airline']['AirlineName'] }}</span>
                                                                        </div>
                                                                        <i class="fas fa-plane"></i>
                                                                    </div>
                                                                    <div class="flight-time">
                                                                        <div class="time">
                                                                            {{ \Carbon\Carbon::parse($segment['Destination']['ArrTime'])->format('h:i A') }}
                                                                        </div>
                                                                        <div class="time-code">
                                                                            {{ $segment['Destination']['Airport']['CityName'] }}
                                                                            ({{ $segment['Destination']['Airport']['CityCode'] }})
                                                                        </div>
                                                                    </div>
                                                                @endforeach

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-section segment{{ $segmentKey }}_{{ $keyFlight }} price-section"
                                                        style="{{ $segmentKey == 1 ? 'display:none' : 'display:block' }}">
                                                        <div class="result-card-sub text-danger">
                                                            {{ @$segments[1] ? __('dashboard.Rounded Flight') : __('dashboard.Direct Flight') }}
                                                        </div>
                                                        <div class="result-card-title">
                                                            {{ $flight['Invoice_Amount'] }}
                                                            {{ $flight['Currency'] }}
                                                        </div>
                                                        <div class="result-card-sub">
                                                        </div>
                                                    </div>

                                                @endforeach

                                                <a href="/select-flight?result={{ $flight['ResultIndex'] }}&segment={{ urlencode(json_encode($flight['Segments'])) }}&invoice_amount={{ $flight['Invoice_Amount'] }}"
                                                    class="btn btn-warning w-100 text-center rounded-0">{{ __('dashboard.Select This') }}</a>
                                            </div>
                                        @empty
                                            <div class="row justify-content-center">
                                                <div class="col-md-10">
                                                    <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeIn"
                                                        data-wow-delay="0.2s"
                                                        style="visibility: visible; animation-delay: 0.2s; animation-name: fadeIn;padding: 100px 30px;">
                                                        <div class="d-flex align-items-center justify-content-center flex-column">
                                                            <i class="fas fa-plane text-warning fa-3x mb-3"></i>
                                                            <h2 class="prepare-title mb-3">
                                                                {{ __('dashboard.No Found Flights') }}
                                                            </h2>
                                                            <p class="mb-4 text-muted">
                                                                {{ __('dashboard.Sorry, no flights were found for your search criteria. Please try:') }}
                                                            </p>
                                                            <div class="suggestions text-left">
                                                                <ul class="list-unstyled">
                                                                    <li class="mb-2"><i class="fas fa-check text-success mx-2"></i>{{ __('dashboard.Try different dates') }}</li>
                                                                    <li class="mb-2"><i class="fas fa-check text-success mx-2"></i>{{ __('dashboard.Check for flexible travel dates') }}</li>
                                                                    <li class="mb-2"><i class="fas fa-check text-success mx-2"></i>{{ __('dashboard.Consider nearby airports') }}</li>
                                                                    <li class="mb-2"><i class="fas fa-check text-success mx-2"></i>{{ __('dashboard.Try removing some filters') }}</li>
                                                                </ul>
                                                            </div>
                                                            <div class="mt-4">
                                                                <a href="{{ url('/ai-trip') }}" class="btn btn-warning me-2">
                                                                    <i class="fas fa-search mx-1"></i>{{ __('dashboard.New Search') }}
                                                                </a>
                                                                <button type="button" class="btn btn-outline-warning" onclick="window.history.back()">
                                                                    <i class="fas fa-arrow-left mx-1"></i>{{ __('dashboard.Go Back') }}
                                                                </button>
                                                            </div>
                                                            
                                                            {{-- @if(auth()->check() && auth()->user()->user_type_id == 1)
                                                            <div class="mt-3">
                                                                <small class="text-muted">
                                                                    <strong>Debug Info (Admin Only):</strong><br>
                                                                    Route: {{ request()->origin }} → {{ request()->destination }}<br>
                                                                    Dates: {{ request()->start_date }} to {{ request()->end_date }}<br>
                                                                    Passengers: {{ request()->adults ?? 1 }} adults, {{ request()->children ?? 0 }} children<br>
                                                                    API: TBO Flight Search
                                                                </small>
                                                            </div>
                                                            @endif --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>

                                    {{ $flights->appends(request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- search form End -->

    <div id="expired_modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <p style="font-weight: 900;margin: 0px;">{{ __('messages.Please refresh your search for the latest prices') }}</p>
            </div>
            <p>{{ __('messages.Flight prices change frequently due to availability and demand. We want to make sure you always see the most recent prices available') }}
            </p>
            <button id="redirectBtn" class="btn">{{ __('messages.refresh') }}</button>
        </div>
    </div>

    <style>
        .airway-details {
            display: flex;
            align-items: center;
            justify-content: stretch;
            gap: 20px;
            justify-content: space-between;
            margin-bottom: 2em;
            flex-wrap: nowrap;
        }

        /* Filter sidebar responsive */
        @media (max-width: 768px) {
            .filter-sidebar {
                position: fixed;
                left: -100%;
                top: 0;
                width: 80%;
                height: 100vh;
                background: white;
                z-index: 1000;
                transition: left 0.3s ease;
                padding: 20px;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                overflow-y: auto;
            }
            
            .filter-sidebar.show {
                left: 0;
            }
            
            .filter-btn {
                display: block !important;
            }
            
            .remove-class {
                display: block;
                text-align: right;
                margin-bottom: 20px;
            }
            
            /* Filter overlay */
            .filter-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            
            .filter-overlay.show {
                display: block;
            }
        }
        
        @media (min-width: 769px) {
            .filter-btn {
                display: none !important;
            }
            
            .remove-class {
                display: none;
            }
        }

        /* Modal styling */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /*z-index: 1;*/
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            /* Black background with opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            text-align: center;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        #redirectBtn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        #redirectBtn:hover {
            background-color: #45a049;
        }
    </style>

    <style>
        .result-cards .btn {
            flex-basis: 100%;
            padding: 5px 1em;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Handle filter form submission
            $('.steps-form input[type="checkbox"], .steps-form input[type="radio"]').on('change', function() {
                // Show loading indicator
                $('body').append('<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;"><div><i class="fas fa-spinner fa-spin"></i> Loading...</div></div>');
                
                // Auto-submit form when filter changes
                $('.steps-form')[0].submit();
            });
            
            // Handle reset button
            $('#resetbtn').on('click', function(e) {
                e.preventDefault();
                
                // Show loading indicator
                $('body').append('<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;"><div><i class="fas fa-spinner fa-spin"></i> Resetting...</div></div>');
                
                // Get the current URL without query parameters but keep essential ones
                var baseUrl = window.location.origin + window.location.pathname;
                var essentialParams = new URLSearchParams();
                
                // Preserve essential search parameters
                var currentParams = new URLSearchParams(window.location.search);
                if (currentParams.get('origin')) essentialParams.set('origin', currentParams.get('origin'));
                if (currentParams.get('destination')) essentialParams.set('destination', currentParams.get('destination'));
                if (currentParams.get('start_date')) essentialParams.set('start_date', currentParams.get('start_date'));
                if (currentParams.get('end_date')) essentialParams.set('end_date', currentParams.get('end_date'));
                if (currentParams.get('type')) essentialParams.set('type', currentParams.get('type'));
                if (currentParams.get('adults')) essentialParams.set('adults', currentParams.get('adults'));
                if (currentParams.get('children')) essentialParams.set('children', currentParams.get('children'));
                if (currentParams.get('infants')) essentialParams.set('infants', currentParams.get('infants'));
                
                // Redirect to clean URL with only essential parameters
                window.location.href = baseUrl + (essentialParams.toString() ? '?' + essentialParams.toString() : '');
            });
            
            // Handle filter sidebar toggle
            $('.filter-btn').on('click', function() {
                $('.filter-sidebar').toggleClass('show');
                $('.filter-overlay').toggleClass('show');
            });
            
            // Handle remove filter button and overlay click
            $('.remove-class-link, .filter-overlay').on('click', function(e) {
                e.preventDefault();
                $('.filter-sidebar').removeClass('show');
                $('.filter-overlay').removeClass('show');
            });
        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            $(".selectType").click(function() {
                var id = $(this).attr('id');

                if ($(this).hasClass('btn-primary')) {
                    var type = 1;
                    var segment = 'segment' + type + '_' + id;
                    $('.' + segment).show();
                    var type = 0;
                    var segment = 'segment' + type + '_' + id;
                    $('.' + segment).hide();
                }

                if ($(this).hasClass('btn-danger')) {
                    var type = 0;
                    var segment = 'segment' + type + '_' + id;
                    $('.' + segment).show();
                    var type = 1;
                    var segment = 'segment' + type + '_' + id;
                    $('.' + segment).hide();
                }

            });
        });
    </script> --}}
    <script>
        $(document).ready(function() {
            var expiry_flight_time =
                '{{ isset(session('flight')['flight_expired_time']) ? session('flight')['flight_expired_time'] : 0 }}';

            if (expiry_flight_time) {
                var expiry_flight_time = new Date(expiry_flight_time);
                setInterval(function() {
                    var current_time = new Date();
                    var time_difference = expiry_flight_time - current_time;
                    var minutes = Math.floor(time_difference / 1000 / 60);
                    var seconds = Math.floor(time_difference / 1000) % 60;

                    if (time_difference < 0) {
                        $('#expired_modal').show();
                        return;
                    }
                    console.log(current_time);
                }, 60000);

            }
        });
        $(document).ready(function() {
            // Get modal and buttons
            var modal = $('#myModal');
            var openBtn = $('#openModalBtn');
            var closeBtn = $('.close');
            var redirectBtn = $('#redirectBtn');

            // Open modal on button click
            openBtn.on('click', function() {
                modal.show();
            });

            // Close modal when clicking the 'X'
            closeBtn.on('click', function() {
                modal.hide();
            });

            // Close modal when clicking outside of it
            $(window).on('click', function(event) {
                if ($(event.target).is(modal)) {
                    modal.hide();
                }
            });

            // Redirect on button click
            redirectBtn.on('click', function() {
                window.location.href = '/flights';
            });
        });
    </script>

@endsection
