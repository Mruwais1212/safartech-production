@extends('website.layout')
@section('title')
    {{ app()->getLocale() == 'ar' ? $trip->country->name_ar : $trip->country->name_en }},
    {{ app()->getLocale() == 'ar' ? $trip->city->name_ar : $trip->city->name_en }}
@endsection
@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/trip.png');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div>

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">
                                {{ app()->getLocale() == 'ar' ? $trip->country->name_ar : $trip->country->name_en }}
                                ,
                                {{ app()->getLocale() == 'ar' ? $trip->city->name_ar : $trip->city->name_en }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- steps form -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="plan-section mt-5">
                                <div class="plan-gallery">
                                    <div class="container p-0">
                                        <div class="row justify-content-start">
                                            <div class="col col-md-12 gallery-container-wrap position-relative">
                                                <div class="gallery-container" style="display: block;"
                                                    id="gallery-dynamic-thumbnails">
                                                    <a class="gallery-item" data-index="0"
                                                        data-src="{{ is_file('uploads/' . $trip->image) ? asset('uploads/' . $trip->image) : '/site/img/property-1.jpg' }}">
                                                        <img alt="layers of blue." class="img-responsive" style="max-height: 400px;"
                                                            src="{{ is_file('uploads/' . $trip->image) ? asset('uploads/' . $trip->image) : '/site/img/property-1.jpg' }}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="plan-actions">
                                    <div class="plan-title">
                                        <h1 class="my-2">
                                            {{ app()->getLocale() == 'ar' ? $trip->country->name_ar : $trip->country->name_en }}
                                            ,
                                            {{ app()->getLocale() == 'ar' ? $trip->city->name_ar : $trip->city->name_en }}
                                        </h1>
                                    </div>
                                </div>
                                <hr>
                                <div class="plan-details">
                                    <div class="plan-details-title">
                                        <h6 class="my-2">{{ __('dashboard.Traveling details') }}</h6>
                                    </div>
                                    <p class="details">
                                        {!! app()->getLocale() == 'ar' ? $trip->description_ar : $trip->description_en !!}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row col-md-6">
                            <div class="col-md-12">
                                <div class="form-group text-start">
                                    <label class="fw-bold mb-3" for="grid">
                                        {{ __('dashboard.choose the type of reservation') }}
                                        <span id="trip-type-error" style="padding-right: 180px;" class="text-danger"></span>
                                    </label>
                                </div>
                                <div id="grid" class="grid">
                                    <label class="card">
                                        <input name="type" class="radio" show="radio1" type="radio" value="1">
                                        <span class="plan-details">
                                            <img style="object-fit: contain;" width="auto" src="/site/img/radio1.png"
                                                alt="chose type" />
                                            <span class="text-center">
                                                {{ __('dashboard.Hotel and Flight Booking') }}
                                            </span>
                                        </span>
                                    </label>
                                    <label class="card">
                                        <input name="type" class="radio" show="radio2" type="radio" value="2">
                                        <span class="plan-details">
                                            <img style="object-fit: contain;" width="auto" src="/site/img/radio2.png"
                                                alt="chose type" />
                                            <span class="text-center">{{ __('dashboard.Hotel Booking') }}</span>
                                        </span>
                                    </label>
                                    <label class="card">
                                        <input name="type" class="radio" show="radio3" type="radio" value="3">
                                        <span class="plan-details">
                                            <img style="object-fit: contain;" width="auto" src="/site/img/radio3.png"
                                                alt="chose type" />
                                            <span class="text-center">{{ __('dashboard.Flight Booking') }}</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12 radio1 radio-section">
                                <form action="/search-plans" method="POST" id="plans-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="guest_nationality">{{ __('dashboard.Guest Nationality') }}</label>
                                                        <select class="form-control" name="guest_nationality" id="guest_nationality">
                                                           
                                                            <option disabled hidden
                                                            value="" selected>
                                                            {{ __('dashboard.Select Nationality') }}
                                                        </option>
                                                        @foreach (\App\Models\Country::all() as $country)
                                                            <option
                                                                value="{{ $country->code }}">
                                                                {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                            </option>
                                                        @endforeach
                                                        </select>
                                                    </div>
                                                    <span class="text-danger" id="guest-nationality-error"></span>
                                                </div>

                                        <div class="col-md-4">
                                            <div class="form-group dropdown">
                                                <label
                                                    for="exampleInputEmail1">{{ __('dashboard.departure City') }}</label>
                                                <select id="searchableSelect" name="origin" style="width: 300px;"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">

                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="startDate">{{ __('dashboard.Start Date') }}</label>
                                                <div class="date-div">
                                                    <input type="text" class="form-control date" id="startDate"
                                                        value="" name="start_date">
                                                </div>
                                            </div>
                                            <span class="text-danger" id="startDate-error"
                                                style="font-size: x-small;"></span>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="endDate">{{ __('dashboard.End Date') }}</label>
                                                <div class="date-div">
                                                    <input type="text" class="form-control date" id="endDate"
                                                        value="" name="end_date">
                                                </div>
                                            </div>
                                            <span class="text-danger" id="endDate-error"
                                                style="font-size: x-small;"></span>
                                        </div>
                                        {{-- <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="travelrs">{{ __('dashboard.Travelers') }}</label>
                                                <div class="dropdown">
                                                    <input type="text" class="form-control dropdown-input"
                                                        placeholder="{{ __('dashboard.Travelers') }}" readonly>
                                                    <div class="dropdown-menu input-dropdown dropdown-content">
                                                        <div class="px-3 py-2">
                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Adults') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="adults" value="1" id="adults"
                                                                            min="1" max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Children') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="children" value="0" id="children"
                                                                            min="0" max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Babies') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="infants" value="0" id="babies"
                                                                            min="0" max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Rooms') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number" class="form-control"
                                                                            name="rooms" value="1" id="rooms"
                                                                            min="1" max="100" />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <br>
                                                            <button type="button"
                                                                class="btn btn-warning closeButton dropdown-close-button w-100">{{ __('dashboard.Done') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="text-danger" id="adults-error"
                                                    style="font-size: x-small;"></span> <br>
                                                <span class="text-danger" id="flight-class-error"
                                                    style="font-size: x-small;"></span>
                                            </div>
                                        </div> --}}

                                         <div class="col-md-4">
                                       
                                        <x-travelers-input 
                                            prefix=""
                                            
                                            name="rooms"
                                            defaultRooms="1"
                                            label="{{ __('dashboard.Travelers') }}"
                                            
                                        />
                                         </div>

                                        {{-- <div class="col-md-12 my-4">
                                            <div class="form-group">
                                                <label class="mb-3" for="travelrs">{{ __('dashboard.Budget') }}</label>
                                                <input class="range-example-input" type="text" min="1500"
                                                    max="40000" currency="SAR" value="1500,40000" name="budget"
                                                    id="budget" step="1">
                                            </div>
                                        </div> --}}
                                    </div>
                                    <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                                    <a type="submit" href="javascript:void(0)"
                                        class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn" id="search-plans">
                                        {{ __('dashboard.Search') }}
                                    </a>
                                </form>
                            </div>

                            <div class="col-md-12 radio2 radio-section">
                                <form action="/search-hotels-new" method="POST" id="hotel-form">
                                    @csrf
                                    <div class="row">
                                        {{-- <div class="col-md-4">
                                                <div class="form-group">
                                                    <label
                                                        for="exampleInputEmail1">{{ __('dashboard.Arrival Location') }}</label>
                                                    <input type="text" class="form-control" name="destination"
                                                                id="hotel_destination"
                                                                placeholder="{{ __('dashboard.Arrival Location') }}">
                                                    <select id="searchableSelectArrivalLocation" name="destination"
                                                        style="width: 300px;"></select>
                                                </div>
                                                <span class="text-danger" id="hotel-destination-error"></span>
                                            </div> --}}
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="guest_nationality">{{ __('dashboard.Guest Nationality') }}</label>
                                                    <select class="form-control" name="guest_nationality" id="guest_nationality">
                                                        
                                                        <option disabled hidden
                                                        value="" selected>
                                                        {{ __('dashboard.Select Nationality') }}
                                                    </option>
                                                    @foreach (\App\Models\Country::all() as $country)
                                                        <option
                                                            value="{{ $country->code }}">
                                                            {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                        </option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                                <span class="text-danger" id="guest-nationality-error"></span>
                                            </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="startDate">{{ __('dashboard.Check IN Date') }}</label>
                                                <div class="date-div">
                                                    <input type="text" class="form-control date" id="hotel_checkin"
                                                        name="start_date">
                                                </div>
                                                <span class="text-danger" id="hotel-checkin-error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="endDate">{{ __('dashboard.Check Out Date') }}</label>
                                                <div class="date-div">
                                                    <input type="text" class="form-control date" id="hotel_checkout"
                                                        name="end_date">
                                                </div>
                                            </div>
                                            <span class="text-danger" id="hotel-checkout-error"></span>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="travelrs">{{ __('dashboard.Travelers') }}</label>
                                                <div class="dropdown">
                                                    <input type="text" class="form-control dropdown-input"
                                                        placeholder="{{ __('dashboard.Travelers') }}" readonly>
                                                    <div class="dropdown-menu input-dropdown dropdown-content">
                                                        <div class="px-3 py-2">
                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Adults') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="adults" value="1"
                                                                            id="hotel_adults" min="1"
                                                                            max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Children') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="children" value="0"
                                                                            id="hotel_children" min="0"
                                                                            max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Babies') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="infants" value="0"
                                                                            id="hotel_babies" min="0"
                                                                            max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Rooms') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number" class="form-control"
                                                                            name="rooms" value="1"
                                                                            id="hotel_rooms" min="1"
                                                                            max="6" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <button type="button"
                                                                class="btn btn-warning closeButton dropdown-close-button w-100">{{ __('dashboard.Done') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="text-danger" id="adults-error"
                                                    style="font-size: x-small;"></span> <br>
                                                <span class="text-danger" id="flight-class-error"
                                                    style="font-size: x-small;"></span>
                                            </div>
                                            <span class="text-danger" id="hotel-travels-error"></span>
                                        </div> --}}
                                        <div class="col-md-3">
                                       
                                        <x-travelers-input 
                                            prefix="hotel_"
                                            
                                            name="rooms"
                                            defaultRooms="1"
                                            label="{{ __('dashboard.Travelers') }}"
                                            
                                        />
                                         </div>
                                        {{-- <div class="col-md-12 my-4">
                                            <div class="form-group">
                                                <label class="mb-3" for="travelrs">{{ __('dashboard.Budget') }}</label>
                                                <input class="range-example-input" type="text" min="1500"
                                                    max="40000" currency="SAR" value="1500,40000" name="budget"
                                                    id="hotel_budget" step="1">
                                            </div>
                                            <span class="text-danger" id="hotel-budget-error"></span>
                                        </div> --}}
                                    </div>

                                    <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                                    <a type="submit" href="javascript:void(0)"
                                        class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn" id="search-hotels">
                                        {{ __('dashboard.Search Hotels') }}
                                    </a>
                                </form>
                            </div>

                            <div class="col-md-12 radio3 radio-section">
                                <form action="/search-flights-new" method="POST" id="flight-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="guest_nationality">{{ __('dashboard.Guest Nationality') }}</label>
                                                <select class="form-control" name="guest_nationality" id="guest_nationality">
                                                    
                                                    <option disabled hidden
                                                    value="" selected>
                                                    {{ __('dashboard.Select Nationality') }}
                                                </option>
                                                @foreach (\App\Models\Country::all() as $country)
                                                    <option
                                                        value="{{ $country->code }}">
                                                        {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                    </option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <span class="text-danger" id="guest-nationality-error"></span>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label
                                                    for="exampleInputEmail1">{{ __('dashboard.Departure City') }}</label>
                                                {{-- <input type="text" class="form-control" name="origin"
                                                                id="flight_origin"
                                                                placeholder="{{ __('dashboard.Departure City') }}"> --}}
                                                <select id="searchableSelectDepartureCity" name="origin"
                                                    style="width: 300px;"></select>
                                            </div>
                                            <span class="text-danger" id="flight-origin-error"></span>
                                        </div>
                                        <div class="col-md-4 ">
                                            <div class="form-group">
                                                <label for="travelrs">{{ __('dashboard.Flight Type') }}</label>
                                                <select class="form-control" name="journey_type" id="journey_type">
                                                    <option disabled hidden value="">
                                                        {{ __('dashboard.select_flight_type') }}
                                                    </option>
                                                    <option value="1"
                                                        {{ request()->journey_type == '1' ? 'selected' : '' }}>
                                                        {{ __('dashboard.One Way') }}
                                                    </option>
                                                    <option value="2"
                                                        {{ request()->journey_type == '2' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Return') }}
                                                    </option>
                                                    <option
                                                        value="3 {{ request()->journey_type == '3' ? 'selected' : '' }}">
                                                        {{ __('dashboard.Multi Stop') }}
                                                    </option>
                                                    <option value="4"
                                                        {{ request()->journey_type == '4' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Advance Search') }}</option>
                                                    <option value="5"
                                                        {{ request()->journey_type == '5' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Special Return') }}</option>
                                                </select>
                                            </div>
                                            <span class="text-danger" id="flight-journey-type-error"></span>
                                        </div>

                                        {{-- <div class="col-md-4">
                                                <div class="form-group">
                                                    <label
                                                        for="exampleInputEmail1">{{ __('dashboard.Arrival Location') }}</label>
                                                    <input type="text" class="form-control" name="destination"
                                                                id="flight_destination"
                                                                placeholder="{{ __('dashboard.Arrival Location') }}">
                                                    <select id="searchableSelectArrivalCity" name="destination"
                                                        style="width: 300px;"></select>
                                                </div>
                                                <span class="text-danger" id="flight-destination-error"></span>
                                            </div> --}}
                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label for="startDate">{{ __('dashboard.Travel Date') }}</label>
                                                <div class="date-div">
                                                    <input type="text" class="form-control date"id="flight_start_date"
                                                        name="start_date">
                                                    <span class="text-danger" id="flight-start-date-error"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4  mt-2">
                                            <div class="form-group">
                                                <label for="endDate">{{ __('dashboard.Return Date') }}</label>
                                                <div class="date-div">
                                                    <input type="text" class="form-control date" id="flight_end_date"
                                                        name="end_date">
                                                    <span class="text-danger" id="flight-end-date-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mt-2">
                                            <div class="form-group">
                                                <label for="travelrs">{{ __('dashboard.Travelers') }}</label>
                                                <div class="dropdown">
                                                    <input type="text" class="form-control dropdown-input"
                                                        placeholder="{{ __('dashboard.Travelers') }}" readonly>
                                                    <div class="dropdown-menu input-dropdown dropdown-content">
                                                        <div class="px-3 py-2">
                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Adults') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="adults" value="1"
                                                                            id="flight_adults" min="1"
                                                                            max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Children') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="children" value="0"
                                                                            id="flight_children" min="0"
                                                                            max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            <div class="form-group row" style="margin-bottom: 1em;">
                                                                <div class="col-md-5 d-flex align-items-center">
                                                                    <label
                                                                        for="travelrs">{{ __('dashboard.Babies') }}</label>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="custom-number-input">
                                                                        <button type="button" class="decrement-btn">
                                                                            <i class="bi bi-chevron-down"></i>
                                                                        </button>
                                                                        <button type="button" class="increment-btn">
                                                                            <i class="bi bi-chevron-up"></i>
                                                                        </button>
                                                                        <input type="number"
                                                                            class="form-control dropdown-item-input"
                                                                            name="infants" value="0"
                                                                            id="flight_babies" min="0"
                                                                            max="100" />
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <button type="button"
                                                                class="btn btn-warning closeButton dropdown-close-button w-100">{{ __('dashboard.Done') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="text-danger" id="adults-error"
                                                    style="font-size: x-small;"></span> <br>
                                                <span class="text-danger" id="flight-class-error"
                                                    style="font-size: x-small;"></span>
                                            </div>
                                            <span class="text-danger" id="flight-travels-error"></span>
                                        </div>



                                        

                                        {{-- <div class="col-md-12 my-4">
                                            <div class="form-group">
                                                <label class="mb-3" for="travelrs">{{ __('dashboard.Budget') }}</label>
                                                <input class="range-example-input" type="text" min="1500"
                                                    max="40000" currency="SAR" value="1500,40000" name="budget"
                                                    id="flight_budget" step="1">
                                            </div>
                                            <span class="text-danger" id="flight-budget-error"></span>
                                        </div> --}}

                                    </div>
                                    <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                                    <a href="javascript:void(0)"
                                        class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn" id="search-flights">
                                        {{ __('dashboard.Search Flights') }}
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Steps form End -->

    <!-- Property List Start -->
    <div class="container py-5 places">
        <div class="container">
            <div class="row g-4">
                <div class="text-start mx-auto wow fadeInUp p-0" data-wow-delay="0.1s" style="max-width: 100%;">
                    <h1 class="mb-1 section-title pb-2 lined-title aftrer-title">
                        {{ __('dashboard.Places To Visit') }}
                    </h1>
                </div>
                @foreach ($trip->places as $place)
                    <div class="col-lg-3 col-md-4 col-6 wow fadeInUp mb-3" data-wow-delay="0.1s">
                        <div class="property-item place-item overflow-hidden mb-3">
                            <div class="position-relative overflow-hidden">
                                <a href="">
                                    <img src="{{ is_file('uploads/' . $place->image) ? asset('uploads/' . $place->image) : '/site/img/property-1.jpg' }}"
                                        alt="" style="height: 350px;" class="img-fluid">
                                </a>
                            </div>
                        </div>
                        <a href="#" class="place-title">
                            {{ app()->getLocale() == 'ar' ? $place->name_ar : $place->name_en }}
                        </a>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <!-- Property List End -->


    <!-- Property List Start -->
    <div class="container py-5">
        <div class="container">
            <div class="row g-4">
                <div class="text-start mx-auto wow fadeInUp p-0" data-wow-delay="0.1s" style="max-width: 100%;">
                    <h1 class="mb-1 section-title pb-2 lined-title aftrer-title">
                        {{ __('dashboard.Related Trips') }}</h1>
                </div>
                @foreach ($relatedTrips as $relatedTrip)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="property-item overflow-hidden">
                            <div class="bg-white text-primary d-flex justify-content-between align-items-center py-3 px-3">
                                <p class="hotel-country m-0">
                                    {{ app()->getLocale() == 'ar' ? $relatedTrip->country->name_ar : $relatedTrip->country->name_en }}
                                    ,
                                    {{ app()->getLocale() == 'ar' ? $relatedTrip->city->name_ar : $relatedTrip->city->name_en }}
                                </p>
                            </div>
                            <div class="position-relative overflow-hidden">
                                <a href="/trips/{{ $relatedTrip->id }}"><img class="img-fluid"
                                        src="{{ is_file('uploads/' . $relatedTrip->image) ? asset('uploads/' . $relatedTrip->image) : '/site/img/property-1.jpg' }}"
                                        alt=""></a>
                            </div>
                            <div class="px-4 py-2 pb-0">
                                <p class="hotel-desc">
                                    {!!  app()->getLocale() == 'ar' ? $relatedTrip->description_ar : $relatedTrip->description_en !!}
                                </p>
                            </div>
                            <div
                                class="px-4 py-2 d-flex justify-content-between align-items-center border-top hotel-bottom">
                                <small class="py-2 w-100">
                                    <a href="/trips/{{ $relatedTrip->id }}"
                                        class="btn btn-warning py-2 px-3 w-100">{{ __('dashboard.view_details') }}</a>
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $(document).ready(function() {
            $('#search-plans').on('click', function() {
                var check = 0;
                if (!$('#startDate').val()) {
                    $('#startDate-error').html("{{ __('dashboard.please select start date') }}");
                    check = 1;
                } else {
                    $('#startDate-error').html("");
                }

                if (!$('#endDate').val()) {
                    $('#endDate-error').html("{{ __('dashboard.please select end date') }}");
                    check = 1;
                } else {
                    $('#endDate-error').html("");
                }

                var start_date = new Date($('#startDate').val());
                var end_date = new Date($('#endDate').val());

                if (start_date >= end_date) {
                    $('#endDate-error').html(
                        "{{ __('dashboard.end date should be greater than start date') }}");
                    check = 1;

                } else {
                    $('#endDate-error').html("");
                }

                // if (!$('#adults').val() || $('#adults').val() == 0) {
                //     $('#adults-error').html("{{ __('dashboard.please select number of adults') }}");
                //     check = 1;

                // } else {
                //     $('#adults-error').html("");
                // }

                if (!$('input[name="type"]:checked').val()) {
                    $('#trip-type-error').html("{{ __('dashboard.please select trip type') }}");
                    check = 1;
                } else {
                    $('#trip-type-error').html("");
                }

                if (check == 1) {
                    return false;
                }

                $('#plans-form').submit();
            });

            $('#search-flights').on('click', function(e) {
                e.preventDefault();

                var check = 0;

                if (!$('#searchableSelectDepartureCity').val()) {
                    check = 1;
                    $('#flight-destination-error').html('{{ __('dashboard.please_select_destination') }}');
                } else {
                    $('#flight-destination-error').html('');
                }

                if (!$('#flight_adults').val()) {
                    check = 1;
                    $('#flight-travels-error').html('{{ __('dashboard.please_select_travelers') }}');
                } else {
                    $('#flight-travels-error').html('');
                }

                if (!$('#flight_start_date').val()) {
                    check = 0;
                    $('#flight-start-date-error').html('{{ __('dashboard.please_select_start_date') }}');
                } else {
                    $('#flight-start-date-error').html('');
                }

                if (!$('#flight_end_date').val()) {
                    check = 1;
                    $('#flight-end-date-error').html('{{ __('dashboard.please_select_end_date') }}');
                } else {
                    $('#flight-end-date-error').html('');
                }

                if (!$('#journey_type').val()) {
                    $('#flight-journey-type-error').html(
                        '{{ __('dashboard.please_select_journey_type') }}');
                } else {
                    $('#flight-journey-type-error').html('');
                }

                var start_date = new Date($('#flight_start_date').val());
                var end_date = new Date($('#flight_end_date').val());

                if (start_date >= end_date) {
                    check = 1;
                    $('#flight-end-date-error').html(
                        "{{ __('dashboard.end date should be greater than start date') }}");
                } else {
                    $('#flight-end-date-error').html('');
                }

                // if (!$('#flight_budget').val()) {
                //     check = 1;
                //     $('#flight-budget-error').html('{{ __('dashboard.please_select_budget') }}');
                // } else {
                //     $('#flight-budget-error').html('');
                // }

                if (check == 1) {
                    return;
                }

                $('#flight-form').submit();
            });

            $('#search-hotels').on('click', function(e) {
                e.preventDefault();

                var check = 0;

                if (!$('#hotel_checkin').val()) {
                    check = 1;
                    $('#hotel-checkin-error').html('{{ __('dashboard.please_select_checkin_date') }}');
                } else {
                    $('#hotel-checkin-error').html('');
                }

                if (!$('#hotel_checkout').val()) {
                    check = 1;
                    $('#hotel-checkout-error').html('{{ __('dashboard.please_select_checkout_date') }}');
                } else {
                    $('#hotel-checkout-error').html('');
                }

                // if (!$('#hotel_adults').val()) {
                //     check = 1;
                //     $('#hotel-travels-error').html('{{ __('dashboard.please_select_travelers') }}');
                // } else {
                //     $('#hotel-travels-error').html('');
                // }


                // if (!$('#hotel_rooms').val()) {
                //     check = 1;
                //     $('#hotel-travels-error').html('{{ __('dashboard.please_select_rooms') }}');
                // } else {
                //     $('#hotel-travels-error').html('');
                // }

                /**
                if (!$('#hotel_budget').val()) {
                    check = 1;
                    $('#hotel-budget-error').html('{{ __('dashboard.please_select_budget') }}');
                } else {
                    $('#hotel-budget-error').html('');
                }
                */

                var start_date = new Date($('#hotel_checkin').val());
                var end_date = new Date($('#hotel_checkout').val());

                if (start_date >= end_date) {
                    check = 1;
                    $('#hotel-checkout-error').html(
                        "{{ __('dashboard.end date should be greater than start date') }}");
                } else {
                    $('#hotel-checkout-error').html('');
                }

                if (check == 1) {
                    return;
                }

                $('#hotel-form').submit();
            });

            $(document).ready(function() {
                $('#searchableSelect').select2({
                    placeholder: 'Search...',
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    ajax: {
                        url: '/search-airport',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.city + ' (' + item.airport_code +
                                            ') ',
                                    }; // Adjust based on API response structure
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#searchableSelectArrivalLocation').select2({
                    placeholder: 'Search...',
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    ajax: {
                        url: '/search-airport',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.city + ' (' + item.airport_code +
                                            ') ',
                                    }; // Adjust based on API response structure
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#searchableSelectArrivalCity').select2({
                    placeholder: 'Search...',
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    ajax: {
                        url: '/search-airport',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.city + ' (' + item.airport_code +
                                            ') ',
                                    }; // Adjust based on API response structure
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#searchableSelectDepartureCity').select2({
                    placeholder: 'Search...',
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    ajax: {
                        url: '/search-airport',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.city + ' (' + item.airport_code +
                                            ') ',
                                    }; // Adjust based on API response structure
                                })
                            };
                        },
                        cache: true
                    }
                });
            });
        });
    </script>
@endsection
