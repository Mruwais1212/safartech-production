@extends('website.layout')
@section('title', __('dashboard.AI Trip Planner'))

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
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.AI Trip Planner') }}</h1>
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
                <form class="steps-form">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="progress-container text-center">
                                    <div class="progress" id="progress" style="width:0%;"></div>
                                    <div class="text-wrap active">
                                        <div class="circle">01</div>
                                        <p class="text">{{ __('dashboard.Plan') }}</p>
                                    </div>
                                    <div class="text-wrap" id="second-step">
                                        <div class="circle">02</div>
                                        <p class="text">{{ __('dashboard.Compare') }}</p>
                                    </div>
                                    <div class="text-wrap">
                                        <div class="circle">03</div>
                                        <p class="text">{{ __('dashboard.Select') }}</p>
                                    </div>
                                    <div class="text-wrap">
                                        <div class="circle">04</div>
                                        <p class="text">{{ __('dashboard.Payment') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container search-page">
        <div class="row">
            <div class="col-md-12">
                {{-- <form class="steps-form"> --}}
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s"
                                style="max-width: 100%;">
                                <h1 class="mb-3 section-title">
                                    {{ __('dashboard.Plan Your Dream Trip, Effortlessly') }}
                                </h1>
                            </div>
                            <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.2s">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group text-start">
                                                <label class="fw-bold mb-3" for="grid">
                                                    {{ __('dashboard.choose the type of reservation') }}
                                                    <span id="trip-type-error" style="padding-right: 180px;"
                                                        class="text-danger"></span>
                                                </label>
                                            </div>
                                            <div id="grid" class="grid">
                                                <label class="card">
                                                    <input name="type" class="radio" show="radio1" type="radio"
                                                        value="1" checked>
                                                    <span class="plan-details">
                                                        <img style="object-fit: contain;" width="auto"
                                                            src="/site/img/radio1.png" alt="chose type" />
                                                        <span class="text-center">
                                                            {{ __('dashboard.Hotel and Flight Booking') }}
                                                        </span>
                                                    </span>
                                                </label>
                                                <label class="card">
                                                    <input name="type" class="radio" show="radio2" type="radio"
                                                        value="2">
                                                    <span class="plan-details">
                                                        <img style="object-fit: contain;" width="auto"
                                                            src="/site/img/radio2.png" alt="chose type" />
                                                        <span class="text-center">{{ __('dashboard.Hotel Booking') }}</span>
                                                    </span>
                                                </label>
                                                <label class="card">
                                                    <input name="type" class="radio" show="radio3" type="radio"
                                                        value="3">
                                                    <span class="plan-details">
                                                        <img style="object-fit: contain;" width="auto"
                                                            src="/site/img/radio3.png" alt="chose type" />
                                                        <span
                                                            class="text-center">{{ __('dashboard.Flight Booking') }}</span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-4"></div>

                                        <div class="col-md-12 radio1 radio-section">
                                            <div class="row" id="ai-trip">
                                                <div class="col-md-6">
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
                                                {{-- <div class="com-md-6">

                                                </div> --}}

                                                <div class="col-md-6">
                                                    <div class="form-group dropdown">
                                                        <label
                                                            for="exampleInputEmail1">{{ __('dashboard.departure City') }}</label>
                                                        <select id="searchableSelect" name="origin"
                                                            style="width: 300px;"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="startDate">{{ __('dashboard.Start Date') }}</label>
                                                        <div class="date-div">
                                                            <input type="text" class="form-control date"
                                                                id="startDate" value="" name="start_date">
                                                        </div>
                                                    </div>
                                                    <span class="text-danger" id="startDate-error"
                                                        style="font-size: x-small;"></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="endDate">{{ __('dashboard.End Date') }}</label>
                                                        <div class="date-div">
                                                            <input type="text" class="form-control date"
                                                                id="endDate" value="" name="end_date">
                                                        </div>
                                                    </div>
                                                    <span class="text-danger" id="endDate-error"
                                                        style="font-size: x-small;"></span>
                                                </div>
                                                <div class="col-md-4 radio1 radio-section">
                                                    {{-- <div class="form-group">
                                                        <label for="travelrs">{{ __('dashboard.Travelers') }}</label>
                                                        <div class="dropdown">
                                                            <input type="text" class="form-control dropdown-input"
                                                                placeholder="{{ __('dashboard.Travelers') }}" readonly>
                                                            <div class="dropdown-menu input-dropdown dropdown-content">
                                                                <div class="px-3 py-2">
                                                                    <!-- Rooms Input -->
                                                                    <div class="form-group row">
                                                                        <div class="col-md-5 d-flex align-items-center">
                                                                            <label for="rooms">{{ __('dashboard.Rooms') }}</label>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <div class="custom-number-input">
                                                                                <button type="button" class="decrement-btn" data-target="rooms">
                                                                                    <i class="bi bi-chevron-down"></i>
                                                                                </button>
                                                                                <button type="button" class="increment-btn" data-target="rooms">
                                                                                    <i class="bi bi-chevron-up"></i>
                                                                                </button>
                                                                                <input type="number" class="form-control room-count-input"
                                                                                    name="rooms" value="1" id="rooms" min="1" max="6" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span class="text-danger" id="rooms-error" style="font-size: x-small;"></span>
                                                                    
                                                                    <!-- Dynamic Room Forms Container -->
                                                                    <div id="rooms-container">
                                                                        <!-- Room forms will be dynamically generated here -->
                                                                    </div>
                                                                    <span class="text-danger" id="rooms-error" style="font-size: x-small;"></span>

                                                                    <br>
                                                                    <button type="button"
                                                                        class="btn btn-warning closeButton dropdown-close-button w-100">{{ __('dashboard.Done') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                     <x-travelers-input 
                                                        prefix=""
                                                        name="rooms"
                                                        defaultRooms="1"
                                                        label="{{ __('dashboard.Travelers') }}"
                                                        :roomData="[]"
                                                    />
                                                </div>

                                                <div class="col-md-12 mt-3">
                                                    <div class="form-group text-start">
                                                        <label for="grid2">
                                                            {{ __('dashboard.Travel Interests') }}
                                                            <span id="plans-error" style="padding-right: 180px;"
                                                                class="text-danger"></span>
                                                        </label>
                                                    </div>
                                                    <div id="grid2" class="grid">
                                                        @foreach ($interests as $interest)
                                                            <label class="card">
                                                                <input name="plans[]" class="radio travel"
                                                                    type="checkbox" value="{{ $interest->id }}" />
                                                                <span class="plan-details">
                                                                    <img style="object-fit: contain; height: 50px"
                                                                        width="auto"
                                                                        src="{{ is_file('uploads/' . $interest->image) ? asset('uploads/' . $interest->image) : asset('images/placeholder.png') }}"
                                                                        alt="{{ $interest->name_en }}" />
                                                                    <span class="text-center">
                                                                        {{ app()->getLocale() == 'ar' ? $interest->name_ar : $interest->name_en }}</span>
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="col-md-12 my-4">
                                                    <div class="form-group">
                                                        <label class="mb-3"
                                                            for="travelrs">{{ __('dashboard.Budget') }}</label>
                                                        {{-- <input class="range-example-input" type="text" min="1500"
                                                            max="40000" currency="SAR" value="1500,40000"
                                                            name="budget" id="budget" step="1"> --}}
                                                        <div id="grid1" class="grid">
                                                            <label class="card">
                                                                <input name="budget" class="radio travel" type="radio"
                                                                    value="1500,3499" />
                                                                <span class="plan-details">
                                                                    <span class="text-center">
                                                                        {{ app()->getLocale() == 'ar' ? ' (1500 - 3499) ريال' : ' (1500 - 3499) SAR' }}</span>
                                                                </span>
                                                            </label>
                                                            <label class="card">
                                                                <input name="budget" class="radio travel" type="radio"
                                                                    value="3500,7499" />
                                                                <span class="plan-details">
                                                                    <span class="text-center">
                                                                        {{ app()->getLocale() == 'ar' ? ' (3500 - 7499) ريال' : ' (3500 - 7499) SAR' }}</span>
                                                                </span>
                                                            </label>
                                                            <label class="card">
                                                                <input name="budget" class="radio travel" type="radio"
                                                                    value="7500,14999" />
                                                                <span class="plan-details">
                                                                    <span class="text-center">
                                                                        {{ app()->getLocale() == 'ar' ? ' (7500 -  14999) ريال' : ' (7500 -  14999) SAR' }}</span>
                                                                </span>
                                                            </label>
                                                            <label class="card">
                                                                <input name="budget" class="radio travel" type="radio"
                                                                    value="15000,400000" />
                                                                <span class="plan-details">
                                                                    <span class="text-center">
                                                                        {{ app()->getLocale() == 'ar' ? ' (+15000) ريال' : ' (+15000) SAR' }}</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 my-4">
                                                    <div class="form-group row">
                                                        <div class="col-md-5 d-flex align-items-center">
                                                            <label class="fw-bold select-label" for="travelrs">
                                                                {{ __('dashboard.Do you have a destination in mind?') }}
                                                            </label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select class="form-control" id="country_id">
                                                                <option disabled hidden value="" selected>
                                                                    {{ __('dashboard.Select country') }}
                                                                </option>
                                                                @foreach ($countries as $country)
                                                                    <option value="{{ $country->id }}">
                                                                        {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <a type="submit" href="javascript:void(0)"
                                                class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn"
                                                id="search-plans">
                                                {{ __('dashboard.Show me the plans') }}
                                            </a>

                                        </div>

                                        <div class="col-md-12 radio2 radio-section">
                                            <form action="/search-hotels-new" method="POST" id="hotel-form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="guest_nationality">{{ __('dashboard.Guest Nationality') }}</label>
                                                            <select class="form-control" required name="guest_nationality" id="guest_nationality2">
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
                                                             
                                                                {{-- <option value="SA" selected>{{ __('dashboard.Saudi Arabia') }}</option>
                                                                <option value="EG" >{{ __('dashboard.Egypt') }}</option>
                                                                <option value="AE">{{ __('dashboard.United Arab Emirates') }}</option>
                                                                <option value="KW">{{ __('dashboard.Kuwait') }}</option>
                                                                <option value="QA">{{ __('dashboard.Qatar') }}</option>
                                                                <option value="BH">{{ __('dashboard.Bahrain') }}</option>
                                                                <option value="OM">{{ __('dashboard.Oman') }}</option>
                                                                <option value="JO">{{ __('dashboard.Jordan') }}</option>
                                                                <option value="LB">{{ __('dashboard.Lebanon') }}</option>
                                                                <option value="SY">{{ __('dashboard.Syria') }}</option>
                                                                <option value="IQ">{{ __('dashboard.Iraq') }}</option>
                                                                <option value="YE">{{ __('dashboard.Yemen') }}</option>
                                                                <option value="LY">{{ __('dashboard.Libya') }}</option>
                                                                <option value="TN">{{ __('dashboard.Tunisia') }}</option>
                                                                <option value="DZ">{{ __('dashboard.Algeria') }}</option>
                                                                <option value="MA">{{ __('dashboard.Morocco') }}</option>
                                                                <option value="SD">{{ __('dashboard.Sudan') }}</option>
                                                                <option value="US">{{ __('dashboard.United States') }}</option>
                                                                <option value="GB">{{ __('dashboard.United Kingdom') }}</option>
                                                                <option value="DE">{{ __('dashboard.Germany') }}</option>
                                                                <option value="FR">{{ __('dashboard.France') }}</option>
                                                                <option value="IT">{{ __('dashboard.Italy') }}</option>
                                                                <option value="ES">{{ __('dashboard.Spain') }}</option>
                                                                <option value="TR">{{ __('dashboard.Turkey') }}</option>
                                                                <option value="IN">{{ __('dashboard.India') }}</option>
                                                                <option value="PK">{{ __('dashboard.Pakistan') }}</option> --}}
                                                            </select>
                                                        </div>
                                                        <span class="text-danger" id="guest-nationality-error"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label
                                                                for="exampleInputEmail1">{{ __('dashboard.Arrival Location') }}</label>
                                                            {{-- <input type="text" class="form-control" name="destination"
                                                                id="hotel_destination"
                                                                placeholder="{{ __('dashboard.Arrival Location') }}"> --}}
                                                            <select id="searchableSelectArrivalLocation"
                                                                name="destination" style="width: 300px;"></select>
                                                        </div>
                                                        <span class="text-danger" id="hotel-destination-error"></span>
                                                    </div>
                                                  
                                                    
                                                </div>
                                                <div class="row mt-3">

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label
                                                                for="startDate">{{ __('dashboard.Check IN Date') }}</label>
                                                            <div class="date-div">
                                                                <input type="text" class="form-control date"
                                                                    id="hotel_checkin" name="start_date">
                                                            </div>
                                                            <span class="text-danger" id="hotel-checkin-error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                    <div class="form-group">
                                                            <label
                                                                for="endDate">{{ __('dashboard.Check Out Date') }}</label>
                                                            <div class="date-div">
                                                                <input type="text" class="form-control date"
                                                                    id="hotel_checkout" name="end_date">
                                                            </div>
                                                        </div>
                                                        <span class="text-danger" id="hotel-checkout-error"></span>
                                                    </div>
                                                    <div class="col-md-4 radio2 radio-section" style="display:none">
                                                        {{-- <div class="form-group">
                                                            <label for="travelrs">{{ __('dashboard.Travelers') }}</label>
                                                            <div class="dropdown">
                                                                <input type="text" class="form-control dropdown-input"
                                                                    placeholder="{{ __('dashboard.Travelers') }}"
                                                                    readonly>
                                                                <div class="dropdown-menu input-dropdown dropdown-content">
                                                                    <div class="px-3 py-2">
                                                                        <!-- Rooms Input -->
                                                                        <div class="form-group row">
                                                                            <div class="col-md-5 d-flex align-items-center">
                                                                                <label for="hotel_rooms">{{ __('dashboard.Rooms') }}</label>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <div class="custom-number-input">
                                                                                    <button type="button" class="decrement-btn" data-target="hotel_rooms">
                                                                                        <i class="bi bi-chevron-down"></i>
                                                                                    </button>
                                                                                    <button type="button" class="increment-btn" data-target="hotel_rooms">
                                                                                        <i class="bi bi-chevron-up"></i>
                                                                                    </button>
                                                                                    <input type="number" class="form-control room-count-input"
                                                                                        name="rooms" value="1" id="hotel_rooms" min="1" max="6" />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <span class="text-danger" id="hotel-rooms-error" style="font-size: x-small;"></span>
                                                                        
                                                                        <!-- Dynamic Room Forms Container -->
                                                                        <div id="hotel-rooms-container">
                                                                            <!-- Room forms will be dynamically generated here -->
                                                                        </div>

                                                                        <br>
                                                                        <button type="button"
                                                                            class="btn btn-warning closeButton dropdown-close-button w-100">{{ __('dashboard.Done') }}</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span class="text-danger" id="hotel-travels-error"></span> --}}
                                                          <x-travelers-input 
                                                                prefix="hotel_"
                                                                name="rooms"
                                                                defaultRooms="1"
                                                                label="{{ __('dashboard.Travelers') }}"
                                                                :roomData="[]"
                                                            />
                                                    </div>

                                                    <div class="col-md-12 my-4">
                                                        <div class="form-group">
                                                            <label class="mb-3"
                                                                for="travelrs">{{ __('dashboard.Budget') }}</label>
                                                            {{-- <input class="range-example-input" type="text"
                                                                min="1500" max="40000" currency="SAR"
                                                                value="1500,40000" name="budget" id="hotel_budget"
                                                                step="1"> --}}
                                                            <div id="grid4" class="grid">
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="1500,3499" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? ' (1500 - 3499) ريال' : ' (1500 - 3499) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="3500,7499" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? ' (3500 - 7499) ريال' : ' (3500 - 7499) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="7500,14999" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? ' (7500 - 14999) ريال' : ' (7500 - 14999) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="15000,500000" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? ' (+15000) ريال' : ' (+15000) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <span class="text-danger" id="hotel-budget-error"></span>
                                                    </div>
                                                </div>

                                                <a type="submit" href="javascript:void(0)"
                                                    class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn"
                                                    id="search-hotels">
                                                    {{ __('dashboard.Search Hotels') }}
                                                </a>
                                            </form>
                                        </div>

                                        <div class="col-md-12 radio3 radio-section">
                                            <form action="/search-flights-new" method="POST" id="flight-form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="guest_nationality">{{ __('dashboard.Guest Nationality') }}</label>
                                                            <select class="form-control" name="guest_nationality" id="guest_nationality3">
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
                                                     
                                                    <div class="col-md-6">
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

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label
                                                                for="exampleInputEmail1">{{ __('dashboard.Arrival Location') }}</label>
                                                            {{-- <input type="text" class="form-control" name="destination"
                                                                id="flight_destination"
                                                                placeholder="{{ __('dashboard.Arrival Location') }}"> --}}
                                                            <select id="searchableSelectArrivalCity" name="destination"
                                                                style="width: 300px;"></select>
                                                        </div>
                                                        <span class="text-danger" id="flight-destination-error"></span>
                                                    </div>

                                                    {{-- <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="travelrs">{{ __('dashboard.Travelers') }}</label>
                                                            <div class="dropdown">
                                                                <input type="text" class="form-control dropdown-input"
                                                                    placeholder="{{ __('dashboard.Travelers') }}"
                                                                    readonly>
                                                                <div class="dropdown-menu input-dropdown dropdown-content">
                                                                    <div class="px-3 py-2">
                                                                        <div class="form-group row">
                                                                            <div
                                                                                class="col-md-5 d-flex align-items-center">
                                                                                <label
                                                                                    for="travelrs">{{ __('dashboard.Adults') }}</label>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <div class="custom-number-input">
                                                                                    <button type="button"
                                                                                        class="decrement-btn" data-target="flight_adults">
                                                                                        <i class="bi bi-chevron-down"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="increment-btn" data-target="flight_adults">
                                                                                        <i class="bi bi-chevron-up"></i>
                                                                                    </button>
                                                                                    <input type="number"
                                                                                        class="form-control dropdown-item-input"
                                                                                        name="adults" value="1"
                                                                                        id="flight_adults" min="1"
                                                                                        max="6" />
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <span class="text-danger" id="flight-adults-error" style="font-size: x-small;"></span>
                                                                        <div class="form-group row">
                                                                            <div
                                                                                class="col-md-5 d-flex align-items-center">
                                                                                <label
                                                                                    for="travelrs">{{ __('dashboard.Children') }}</label>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <div class="custom-number-input">
                                                                                    <button type="button"
                                                                                        class="decrement-btn" data-target="flight_children">
                                                                                        <i class="bi bi-chevron-down"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="increment-btn" data-target="flight_children">
                                                                                        <i class="bi bi-chevron-up"></i>
                                                                                    </button>
                                                                                    <input type="number"
                                                                                        class="form-control dropdown-item-input flight-children-input"
                                                                                        name="children" value="0"
                                                                                        id="flight_children"
                                                                                        min="0" max="4" />
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <span class="text-danger" id="flight-children-error" style="font-size: x-small;"></span>
                                                                        
                                                                        <!-- Flight Children Ages Container -->
                                                                        <div class="flight-children-ages-container" id="flight_children_ages" style="margin-top: 10px; padding: 0 15px;">
                                                                            <!-- Ages will be dynamically generated here -->
                                                                        </div>

                                                                        <div class="form-group row"
                                                                            style="margin-bottom: 1em;">
                                                                            <div
                                                                                class="col-md-5 d-flex align-items-center">
                                                                                <label
                                                                                    for="travelrs">{{ __('dashboard.Babies') }}</label>
                                                                            </div>
                                                                            <div class="col-md-7">
                                                                                <div class="custom-number-input">
                                                                                    <button type="button"
                                                                                        class="decrement-btn" data-target="flight_babies">
                                                                                        <i class="bi bi-chevron-down"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="increment-btn" data-target="flight_babies">
                                                                                        <i class="bi bi-chevron-up"></i>
                                                                                    </button>
                                                                                    <input type="number"
                                                                                        class="form-control dropdown-item-input"
                                                                                        name="infants" value="0"
                                                                                        id="flight_babies" min="0"
                                                                                        max="4" />
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <span class="text-danger" id="flight-babies-error" style="font-size: x-small;"></span>
                                                                        <button type="button"
                                                                            class="btn btn-warning closeButton dropdown-close-button w-100">{{ __('dashboard.Done') }}</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span class="text-danger" id="flight-travels-error"></span>
                                                    </div> --}}

                                                    

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label
                                                                for="travelrs">{{ __('dashboard.Flight Type') }}</label>
                                                            <select class="form-control" name="journey_type"
                                                                id="journey_type">
                                                                <option disabled hidden value="">
                                                                    {{ __('dashboard.select_flight_type') }}</option>
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

                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group">
                                                            <label
                                                                for="startDate">{{ __('dashboard.Travel Date') }}</label>
                                                            <div class="date-div">
                                                                <input type="text"
                                                                    class="form-control date"id="flight_start_date"
                                                                    name="start_date">
                                                                <span class="text-danger"
                                                                    id="flight-start-date-error"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                 
                                                    <div class="col-md-4 one_way mt-2">
                                                        <div class="form-group">
                                                            <label
                                                                for="endDate">{{ __('dashboard.Return Date') }}</label>
                                                            <div class="date-div">
                                                                <input type="text" class="form-control date"
                                                                    id="flight_end_date" name="end_date">
                                                                <span class="text-danger"
                                                                    id="flight-end-date-error"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                     <div class="col-md-4 radio3 radio-section mt-2" style="display:none">
                                                        <x-travelers-input 
                                                            prefix="flight_"
                                                            name="adults"
                                                            nameChildren="children"
                                                            nameBabies="babies"
                                                            defaultAdults="1"
                                                            defaultChildren="0"
                                                            defaultBabies="0"
                                                            label="{{ __('dashboard.Travelers') }}"
                                                            showPassengersOnly="true"
                                                        />
                                                    </div>


                                                    <div class="col-md-12 my-4">
                                                        <div class="form-group">
                                                            <label class="mb-3"
                                                                for="travelrs">{{ __('dashboard.Budget') }}</label>
                                                            <div id="grid3" class="grid">
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="1500,3499" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? ' (1500 - 3499) ريال' : ' (1500 - 3499) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="3500,7499" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? ' (3500 - 7499) ريال' : ' (3500 - 7499) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="7500,14999" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? ' (7500 - 14999) ريال' : ' (7500 - 14999) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="15000,400000" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? ' (+15000) ريال' : ' (+15000) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            {{-- <input class="range-example-input" type="text"
                                                                min="1500" max="40000" currency="SAR"
                                                                value="1500,40000" name="budget" id="flight_budget"
                                                                step="1"> --}}
                                                        </div>
                                                        <span class="text-danger" id="flight-budget-error"></span>
                                                    </div>

                                                </div>

                                                <a href="javascript:void(0)"
                                                    class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn"
                                                    id="search-flights">
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
                {{-- </form> --}}
            </div>
        </div>
    </div>

    <div class="container prepare-page" style="display: none">
        <div class="row">
            <div class="col-md-12">
                <!-- <form class="steps-form"> -->
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.2s">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div id="wrap">
                                        <div class="ball"></div>
                                        <div class="ball"></div>
                                        <div class="ball"></div>
                                    </div>
                                </div>
                                <h2 class="prepare-title">{{ __('dashboard.Preparing Your Plans') }}</h2>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>

    <div class="container plans-page" style="display: none">
        <div class="row">
            <div class="col-md-12">
                <div class="container">
                    <div class="row justify-content-center">
                        <!-- Property List Start -->
                        <div class="container py-5">
                            <div class="container">
                                <div class="row g-4">
                                    <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s"
                                        style="max-width: 100%;">
                                        <h1 class="mb-1 section-title pb-2 lined-title">
                                            {{ __('dashboard.Suggestions Plans') }}</h1>
                                    </div>
                                    <div id="plans-container" class="row"></div>
                                    <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s"
                                        style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
                                        <a class="btn btn-warning py-3 px-5"
                                            href="/ai-trip">{{ __('dashboard.Generate A new plan') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Property List End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <!-- Testimonial Start -->
    <div style="background-color: #FCFCFC;" class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="mb-3 section-title">What our clients say</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item bg-light rounded">
                    <div class="bg-white border rounded p-4">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="site/img/testimonial-1.jpg"
                                style="width: 45px; height: 45px;">
                            <div class="ps-3">
                                <h6 class="fw-bold mb-1">Client Name</h6>
                            </div>
                        </div>
                        <input class="star-rate rating rating-loading" data-min="0" data-max="5" data-step="1"
                            value="2" data-size="xs" disabled="">
                        <p>
                            “Lorem ipsum dolor sit amet dolor sit consectetur eget maecenas sapien fusce egestas
                            risus purus suspendisse turpis volutpat onare”
                        </p>
                    </div>
                </div>

                <div class="testimonial-item bg-light rounded">
                    <div class="bg-white border rounded p-4">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="/site/img/testimonial-1.jpg"
                                style="width: 45px; height: 45px;">
                            <div class="ps-3">
                                <h6 class="fw-bold mb-1">Client Name</h6>
                            </div>
                        </div>
                        <input class="star-rate rating rating-loading" data-min="0" data-max="5" data-step="1"
                            value="2" data-size="xs" disabled="">
                        <p>
                            “Lorem ipsum dolor sit amet dolor sit consectetur eget maecenas sapien fusce egestas
                            risus purus suspendisse turpis volutpat onare”
                        </p>
                    </div>
                </div>

                <div class="testimonial-item bg-light rounded">
                    <div class="bg-white border rounded p-4">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="/site/img/testimonial-1.jpg"
                                style="width: 45px; height: 45px;">
                            <div class="ps-3">
                                <h6 class="fw-bold mb-1">Client Name</h6>
                            </div>
                        </div>
                        <input class="star-rate rating rating-loading" data-min="0" data-max="5" data-step="1"
                            value="2" data-size="xs" disabled="">
                        <p>
                            “Lorem ipsum dolor sit amet dolor sit consectetur eget maecenas sapien fusce egestas
                            risus purus suspendisse turpis volutpat onare”
                        </p>
                    </div>
                </div>

                <div class="testimonial-item bg-light rounded">
                    <div class="bg-white border rounded p-4">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="/site/img/testimonial-1.jpg"
                                style="width: 45px; height: 45px;">
                            <div class="ps-3">
                                <h6 class="fw-bold mb-1">Client Name</h6>
                            </div>
                        </div>
                        <input class="star-rate rating rating-loading" data-min="0" data-max="5" data-step="1"
                            value="2" data-size="xs" disabled="">
                        <p>
                            “Lorem ipsum dolor sit amet dolor sit consectetur eget maecenas sapien fusce egestas
                            risus purus suspendisse turpis volutpat onare”
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <style>
        .text-danger {
            font-size: small !important;
        }
        
        .room-section {
            margin-bottom: 15px;
        }
        
        .room-section h6 {
            color: #f39c12 !important;
            font-weight: bold !important;
            margin-bottom: 10px !important;
        }
        
        .room-section hr {
            border-color: #f39c12;
            opacity: 0.3;
        }
        
        .room-section .form-group {
            margin-bottom: 8px;
        }
        
        .dropdown-content {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        function getTravelersData(prefix = '') {
            const data = {};
            
            // Check which version we're using (passengers-only or with rooms)
            const isPassengersOnly = $('#' + prefix + 'adults').length > 0;

            if (isPassengersOnly) {
                // For passengers-only version, we'll use "room_1_" as the default prefix
                data[`room_1_adults`] = parseInt($('#' + prefix + 'adults').val()) || 0;
                data[`room_1_children`] = parseInt($('#' + prefix + 'children').val()) || 0;
                data[`room_1_babies`] = parseInt($('#' + prefix + 'babies').val()) || 0;
                
                // Collect children ages
                data[`room_1_child_ages`] = [];
                $('#' + prefix + 'children_ages select').each(function() {
                    data[`room_1_child_ages`].push(parseInt($(this).val()) || 0);
                });
            } else {
                // Full version with rooms - get total rooms count
                const roomsCount = parseInt($('#' + prefix + 'rooms').val()) || 0;
                
                // Collect data for each room
                for (let i = 1; i <= roomsCount; i++) {
                    data[`room_${i}_adults`] = parseInt($('#' + prefix + `room_${i}_adults`).val()) || 0;
                    data[`room_${i}_children`] = parseInt($('#' + prefix + `room_${i}_children`).val()) || 0;
                    data[`room_${i}_babies`] = parseInt($('#' + prefix + `room_${i}_babies`).val()) || 0;
                    
                    // Collect children ages for this room
                    data[`room_${i}_child_ages`] = [];
                    $('#' + prefix + `room_${i}_children_ages select`).each(function() {
                        data[`room_${i}_child_ages`].push(parseInt($(this).val()) || 0);
                    });
                }
            }
            
            return data;
        }

// Usage examples:
// For passengers-only form:
// const travelersData = getTravelersData(); 
// Returns: {room_1_adults: 2, room_1_children: 1, room_1_babies: 0, room_1_child_ages: [5]}

// For rooms form:
// const travelersData = getTravelersData();
// Returns: {
//   room_1_adults: 2,
//   room_1_children: 1,
//   room_1_babies: 0,
//   room_1_child_ages: [5],
//   room_2_adults: 1,
//   room_2_children: 0,
//   room_2_babies: 1,
//   room_2_child_ages: []
// }

// Usage example:
// const travelersData = getTravelersData(); // Without prefix
// const travelersData = getTravelersData('some_prefix_'); // With prefix
        $(document).ready(function() {
            $('#search-plans').on('click', function() {
                var check = 0;
                if (!$('#ai-trip #startDate').val()) {
                    $('#startDate-error').html("{{ __('dashboard.please select start date') }}");
                    check = 1;
                } else {
                    $('#startDate-error').html("");
                }

                // if (!$('#ai-trip #endDate').val()) {
                //     $('#endDate-error').html("{{ __('dashboard.please select end date') }}");
                //     check = 1;
                // } else {
                //     $('#endDate-error').html("");
                // }

                var start_date = new Date($('#ai-trip #startDate').val());
                var end_date = new Date($('#ai-trip #endDate').val());

                if ($('#ai-trip #endDate').val()) {
                    if (start_date >= end_date) {
                        $('#endDate-error').html(
                            "{{ __('dashboard.end date should be greater than start date') }}");
                        check = 1;

                    } else {
                        $('#endDate-error').html("");
                    }
                }


                // Validate room inputs - check that at least one adult is selected across all rooms
                var totalAdults = 0;
                var roomsCount = parseInt($('#ai-trip #rooms').val()) || 1;
                var roomValidationError = false;
                
                for (let i = 1; i <= roomsCount; i++) {
                    var roomAdults = parseInt($(`#ai-trip #room_${i}_adults`).val()) || 0;
                    totalAdults += roomAdults;
                    
                    if (roomAdults === 0) {
                        $(`#room-${i}-adults-error`).html("{{ __('dashboard.At least 1 adult required per room') }}");
                        roomValidationError = true;
                        check = 1;
                    } else {
                        $(`#room-${i}-adults-error`).html("");
                    }
                }
                
                if (totalAdults === 0 && !roomValidationError) {
                    $('#rooms-error').html("{{ __('dashboard.At least 1 adult required') }}");
                    check = 1;
                } else if (!roomValidationError) {
                    $('#rooms-error').html("");
                }

                if (!$('input[name="type"]:checked').val()) {
                    $('#trip-type-error').html("{{ __('dashboard.please select trip type') }}");
                    check = 1;
                } else {
                    $('#trip-type-error').html("");
                }

                if (!$('#ai-trip input[name="plans[]"]:checked').val()) {
                    $('#plans-error').html("{{ __('dashboard.please select plans') }}");
                    check = 1;
                } else {
                    $('#plans-error').html("");
                }

                if (check == 1) {
                    return false;
                }

                // Collect room data
                var roomsData = [];
                var roomsCount = parseInt($('#ai-trip #rooms').val()) || 1;
                
                // Collect individual room data for rooms_data array
                for (let i = 1; i <= roomsCount; i++) {
                    roomsData.push({
                        room_number: i,
                        adults: parseInt($(`#ai-trip #room_${i}_adults`).val()) || 0,
                        children: parseInt($(`#ai-trip #room_${i}_children`).val()) || 0,
                        babies: parseInt($(`#ai-trip #room_${i}_babies`).val()) || 0
                    });
                }

                // Collect individual room field values
                var roomFieldsData = getTravelersData();
                // for (let i = 1; i <= roomsCount; i++) {
                //     let childCount = parseInt($(`#ai-trip #room_${i}_children`).val()) || 0;
                //     let childAge = [];
                //     for (let index = 0; index < childCount; index++) {
                //         const number = childCount[index]; // room_1_child_1_age
                //         childAge.push($(`#ai-trip #room_${i}_child_${index}_age`).val());
                //     }
                //     roomFieldsData[`room_${i}_adults`] = parseInt($(`#ai-trip #room_${i}_adults`).val()) || 0;
                //     roomFieldsData[`room_${i}_children`] = childCount;
                //     roomFieldsData[`room_${i}_babies`] = parseInt($(`#ai-trip #room_${i}_babies`).val()) || 0;
                //     roomFieldsData[`room_${i}_child_ages`] = childAge || [];
                // }
                //return;
                var plans = [];
                $.each($("#ai-trip input[name='plans[]']:checked"), function() {
                    plans.push($(this).val());
                });
                $('.search-page').hide();
                $('.prepare-page').show();
                lang = "{{ app()->getLocale() }}";
                event.preventDefault();
                $.ajax({
                    type: "post",
                    url: '/ai-trip',
                    data: {
                        'start_date': $('#ai-trip #startDate').val(),
                        'origin': $('#ai-trip #searchableSelect').val(),
                        'end_date': $('#ai-trip #endDate').val(),
                        'budget': $("#ai-trip input[name='budget']:checked").val(),
                        'plans': plans,
                        'rooms': $('#ai-trip #rooms').val(),
                        'rooms_data': roomsData,
                        'nationality': $('#ai-trip #guest_nationality').val(),
                        ...roomFieldsData, // Spread the individual room field values
                        'country_id': $('#ai-trip #country_id').val(),
                        'flight_class': $('#ai-trip #flight_class').val(),
                        'trip_type': $('input[name="type"]:checked').val(),
                        '_token': '{{ csrf_token() }}',
                    },
                    tryCount: 0,
                    retryLimit: 2,
                    dataType: "json",
                    success: function(response) {
                        $('#progress').css('width', '30%');
                        $('#second-step').addClass('active');
                        $('.search-page').hide();
                        $('.prepare-page').hide();
                        $('.plans-page').show();
                        trips = '';
                        response.data.forEach(function(trip) {
                            trips +=
                                '<div class="col-xxl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s" style="margin-bottom: 12px;">';
                            trips += '<div class="property-item overflow-hidden">';
                            trips +=
                                '<div class="bg-white text-primary d-flex justify-content-between align-items-center py-3 px-3">';
                            trips += '<p class="hotel-country m-0">';
                            trips += trip.country.name;
                            trips += ',';
                            trips += trip.city.name;
                            trips += '</p>';
                            trips += '</div>';
                            trips += '<div class="position-relative overflow-hidden">';
                            trips += '<a href="/plan/' + trip.id + '">';
                            trips += '<img class="img-fluid" src="' + trip.image +
                                '" alt="">';
                            trips += '</a>';
                            trips += '<div class="px-4 py-2 pb-0">';
                            trips +=
                                '<div class="hotel-title text-warning mb-3 d-flex justify-content-between align-items-center">';
                            trips +=
                                '<div class="align-items-center group-travel-interests" style="display: flex; justify-content: start; flex-wrap: wrap;">';
                            trip.travel_interests.forEach(function(interest) {
                                trips += '<p class="travel-interests">';
                                trips += interest.name;
                                trips += '</p>';
                            });
                            trips += '</div>';
                            trips += '</div>';
                            trips += '<p class="hotel-desc">';
                            trips += trip.description;
                            trips += '</p>';
                            trips += '</div>';
                            trips +=
                                '<div class="px-4 py-2 d-flex justify-content-between align-items-center border-top hotel-bottom">';
                            trips += '<small class="py-2 w-100">';
                            trips += '<a href="/plan/' + trip.id +
                                '" class="btn btn-warning py-2 w-100 px-3">{{ __('dashboard.view_details') }}</a>';
                            trips += '</small>';
                            trips += '</div>';
                            trips += '</div>';
                            trips += '</div>';
                            trips += '</div>';
                        });

                        $('#plans-container').html(trips);
                    },
                    error: function(response, textStatus, errorThrown) {
                        if (response.status == 504) {
                            this.tryCount++;
                            if (this.tryCount <= this.retryLimit) {
                                $.ajax(this);
                                return;
                            }
                            return;
                        }
                        $('.search-page').show();
                        $('.prepare-page').hide();
                    }
                });
            });

            $('#search-flights').on('click', function(e) {
                e.preventDefault();

                var check = 0;

                if (!$('#flight-form #searchableSelectArrivalCity').val()) {
                    $('#flight-origin-error').html('{{ __('dashboard.please_select_origin') }}');
                    check = 1;
                } else {
                    $('#flight-origin-error').html('');
                }

                if (!$('#flight-form #searchableSelectDepartureCity').val()) {
                    check = 1;
                    $('#flight-destination-error').html('{{ __('dashboard.please_select_destination') }}');
                } else {
                    $('#flight-destination-error').html('');
                }

                if (!$('#flight-form #flight_adults').val()) {
                    check = 1;
                    $('#flight-travels-error').html('{{ __('dashboard.please_select_travelers') }}');
                } else {
                    $('#flight-travels-error').html('');
                }

                if (!$('#flight-form #flight_start_date').val()) {
                    check = 0;
                    $('#flight-start-date-error').html('{{ __('dashboard.please_select_start_date') }}');
                } else {
                    $('#flight-start-date-error').html('');
                }

                // if (!$('#flight-form #flight_end_date').val()) {
                //     check = 1;
                //     $('#flight-end-date-error').html('{{ __('dashboard.please_select_end_date') }}');
                // } else {
                //     $('#flight-end-date-error').html('');
                // }

                if (!$('#flight-form #journey_type').val()) {
                    $('#flight-journey-type-error').html(
                        '{{ __('dashboard.please_select_journey_type') }}');
                } else {
                    $('#flight-journey-type-error').html('');
                }

                var start_date = new Date($('#flight-form #flight_start_date').val());
                var end_date = new Date($('#flight-form #flight_end_date').val());

                if ($('#flight-form #flight_end_date').val()) {
                    if (start_date >= end_date) {
                        check = 1;
                        $('#flight-end-date-error').html(
                            "{{ __('dashboard.end date should be greater than start date') }}");
                    } else {
                        $('#flight-end-date-error').html('');
                    }
                }


                // if (!$('#flight-form #flight_budget').val()) {
                if (!$("#flight-form input[name='budget']:checked").val()) {
                    check = 1;
                    $('#flight-budget-error').html('{{ __('dashboard.please_select_budget') }}');
                } else {
                    $('#flight-budget-error').html('');
                }

                if (check == 1) {
                    return;
                }

                $('#flight-form').submit();
            });

            $('#search-hotels').on('click', function(e) {
                e.preventDefault();

                var check = 0;

                if (!$('#searchableSelectArrivalLocation').val()) {
                    check = 1;
                    $('#hotel-destination-error').html('{{ __('dashboard.please_select_destination') }}');
                } else {
                    $('#hotel-destination-error').html('');
                }

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

                // Validate hotel room inputs - check that at least one adult is selected across all rooms
                var totalHotelAdults = 0;
                var hotelRoomsCount = parseInt($('#hotel_rooms').val()) || 1;
                var hotelRoomValidationError = false;
                
                for (let i = 1; i <= hotelRoomsCount; i++) {
                    var hotelRoomAdults = parseInt($(`#hotel_room_${i}_adults`).val()) || 0;
                    totalHotelAdults += hotelRoomAdults;
                    
                    if (hotelRoomAdults === 0) {
                        $(`#hotel-room-${i}-adults-error`).html("{{ __('dashboard.At least 1 adult required per room') }}");
                        hotelRoomValidationError = true;
                        check = 1;
                    } else {
                        $(`#hotel-room-${i}-adults-error`).html("");
                    }
                }
                
                if (totalHotelAdults === 0 && !hotelRoomValidationError) {
                    $('#hotel-travels-error').html("{{ __('dashboard.At least 1 adult required') }}");
                    check = 1;
                } else if (!hotelRoomValidationError) {
                    $('#hotel-travels-error').html('');
                }

                if (!$('#hotel_rooms').val()) {
                    check = 1;
                    $('#hotel-travels-error').html('{{ __('dashboard.please_select_rooms') }}');
                } else if (!hotelRoomValidationError) {
                    $('#hotel-travels-error').html('');
                }

                // if (!$('#hotel_budget').val()) {
                if (!$("input[name='budget']:checked").val()) {
                    check = 1;
                    $('#hotel-budget-error').html('{{ __('dashboard.please_select_budget') }}');
                } else {
                    $('#hotel-budget-error').html('');
                }

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

            // Dynamic room generation functionality
            // function generateRoomForms(containerId, roomCount, prefix = '') {
            //     const container = document.getElementById(containerId);
            //     if (!container) {
            //         return;
            //     }
                
            //     // Store existing values before clearing
            //     const existingValues = {};
            //     for (let i = 1; i <= 10; i++) { // Check up to 10 rooms
            //         const adultsInput = document.getElementById(`${prefix}room_${i}_adults`);
            //         const childrenInput = document.getElementById(`${prefix}room_${i}_children`);
            //         const babiesInput = document.getElementById(`${prefix}room_${i}_babies`);
                    
            //         if (adultsInput) {
            //             existingValues[i] = {
            //                 adults: adultsInput.value,
            //                 children: childrenInput ? childrenInput.value : '0',
            //                 babies: babiesInput ? babiesInput.value : '0'
            //             };
            //         }
            //     }
                
            //     container.innerHTML = '';
                
            //     for (let i = 1; i <= roomCount; i++) {
            //         const roomDiv = document.createElement('div');
            //         roomDiv.className = 'room-section';
                    
            //         // Use existing values if available, otherwise use defaults
            //         const adults = existingValues[i] ? existingValues[i].adults : (i === 1 ? '1' : '1');
            //         const children = existingValues[i] ? existingValues[i].children : '0';
            //         const babies = existingValues[i] ? existingValues[i].babies : '0';
                    
            //         roomDiv.innerHTML = `
            //             <hr style="margin: 15px 0;">
            //             <div class="d-flex justify-content-between align-items-center mb-3">
            //                 <h6 class="mb-0" style="color: #f39c12; font-weight: bold;">{{ __('dashboard.Room') }} ${i}</h6>
            //                 ${roomCount > 1 ? `<button type="button" class="btn btn-sm btn-outline-danger delete-room-btn" data-room="${i}" data-prefix="${prefix}" style="padding:6px 8px 2px 8px; font-size: 12px;"><i class="bi bi-trash"></i> {{ __('dashboard.Delete') }}</button>` : ''}
            //             </div>
                        
            //             <!-- Adults for Room ${i} -->
            //             <div class="form-group row">
            //                 <div class="col-md-5 d-flex align-items-center">
            //                     <label>{{ __('dashboard.Adults') }}</label>
            //                 </div>
            //                 <div class="col-md-7">
            //                     <div class="custom-number-input">
            //                         <button type="button" class="decrement-btn" data-target="${prefix}room_${i}_adults">
            //                             <i class="bi bi-chevron-down"></i>
            //                         </button>
            //                         <button type="button" class="increment-btn" data-target="${prefix}room_${i}_adults">
            //                             <i class="bi bi-chevron-up"></i>
            //                         </button>
            //                         <input type="number" class="form-control dropdown-item-input room-input"
            //                             name="${prefix}room_${i}_adults" value="${adults}" id="${prefix}room_${i}_adults" min="1" max="6" />
            //                     </div>
            //                 </div>
            //             </div>
            //             <span class="text-danger" id="${prefix}room-${i}-adults-error" style="font-size: x-small;"></span>
                        
            //             <!-- Children for Room ${i} -->
            //             <div class="form-group row">
            //                 <div class="col-md-5 d-flex align-items-center">
            //                     <label>{{ __('dashboard.Children') }}</label>
            //                 </div>
            //                 <div class="col-md-7">
            //                     <div class="custom-number-input">
            //                         <button type="button" class="decrement-btn" data-target="${prefix}room_${i}_children">
            //                             <i class="bi bi-chevron-down"></i>
            //                         </button>
            //                         <button type="button" class="increment-btn" data-target="${prefix}room_${i}_children">
            //                             <i class="bi bi-chevron-up"></i>
            //                         </button>
            //                         <input type="number" class="form-control dropdown-item-input room-input children-count-input"
            //                             name="${prefix}room_${i}_children" value="${children}" id="${prefix}room_${i}_children" min="0" max="4" 
            //                             data-room="${i}" data-prefix="${prefix}" />
            //                     </div>
            //                 </div>
            //             </div>
            //             <span class="text-danger" id="${prefix}room-${i}-children-error" style="font-size: x-small;"></span>
                        
            //             <!-- Children Ages Container -->
            //             <div class="children-ages-container" id="${prefix}room_${i}_children_ages" style="margin-top: 10px;">
            //                 <!-- Ages will be dynamically generated here -->
            //             </div>
                        
            //             <!-- Babies for Room ${i} -->
            //             <div class="form-group row">
            //                 <div class="col-md-5 d-flex align-items-center">
            //                     <label>{{ __('dashboard.Babies') }}</label>
            //                 </div>
            //                 <div class="col-md-7">
            //                     <div class="custom-number-input">
            //                         <button type="button" class="decrement-btn" data-target="${prefix}room_${i}_babies">
            //                             <i class="bi bi-chevron-down"></i>
            //                         </button>
            //                         <button type="button" class="increment-btn" data-target="${prefix}room_${i}_babies">
            //                             <i class="bi bi-chevron-up"></i>
            //                         </button>
            //                         <input type="number" class="form-control dropdown-item-input room-input"
            //                             name="${prefix}room_${i}_babies" value="${babies}" id="${prefix}room_${i}_babies" min="0" max="4" />
            //                     </div>
            //                 </div>
            //             </div>
            //             <span class="text-danger" id="${prefix}room-${i}-babies-error" style="font-size: x-small;"></span>
            //         `;
            //         container.appendChild(roomDiv);
            //     }
                
            //     // Re-attach event listeners for the new inputs
            //     attachRoomInputListeners();
                
            //     // Generate children age inputs for each room
            //     for (let i = 1; i <= roomCount; i++) {
            //         const childrenCount = parseInt(document.getElementById(`${prefix}room_${i}_children`)?.value) || 0;
            //         generateChildrenAgeInputs(i, childrenCount, prefix);
            //     }
            // }

            // // Function to generate children age input fields
            // function generateChildrenAgeInputs(roomNumber, childrenCount, prefix = '') {
            //     const container = document.getElementById(`${prefix}room_${roomNumber}_children_ages`);
            //     if (!container) return;
                
            //     // Store existing age values before clearing
            //     const existingAges = {};
            //     for (let j = 1; j <= 4; j++) {
            //         const ageInput = document.getElementById(`${prefix}room_${roomNumber}_child_${j}_age`);
            //         if (ageInput) {
            //             existingAges[j] = ageInput.value;
            //         }
            //     }
                
            //     container.innerHTML = '';
                
            //     if (childrenCount > 0) {
            //         const agesDiv = document.createElement('div');
            //         agesDiv.className = 'row';
            //         agesDiv.innerHTML = '<div class="col-12"><small class="text-muted mt-2 d-block text-start">{{ __("dashboard.Children Ages") }}</small></div>';
                    
            //         for (let j = 1; j <= childrenCount; j++) {
            //             const ageValue = existingAges[j] || '';
            //             const ageField = document.createElement('div');
            //             ageField.className = 'col-md-4 mb-2'; // Changed to col-md-4 for 3 per row
                        
            //             // Generate age options from 1 to 18
            //             let ageOptions = '<option value="" disabled selected>{{ __("dashboard.Age") }}</option>';
            //             for (let age = 1; age <= 18; age++) {
            //                 const selected = ageValue == age ? 'selected' : '';
            //                 ageOptions += `<option value="${age}" ${selected}>${age}</option>`;
            //             }
                        
            //             ageField.innerHTML = `
            //                 <div class="form-group my-1">
            //                     <label for="${prefix}room_${roomNumber}_child_${j}_age" class="form-label" style="font-size: 12px;">{{ __('dashboard.Child') }} ${j} {{ __('dashboard.Age') }}</label>
            //                     <select class="form-control form-control-sm child-age-select" 
            //                             id="${prefix}room_${roomNumber}_child_${j}_age" 
            //                             name="${prefix}room_${roomNumber}_child_ages[]" 
            //                             style="font-size: 12px; height: 32px; padding:0px 10px">
            //                         ${ageOptions}
            //                     </select>
            //                     <span class="text-danger child-age-error" style="font-size: 10px;"></span>
            //                 </div>
            //             `;
            //             agesDiv.appendChild(ageField);
            //         }
                    
            //         container.appendChild(agesDiv);
                    
            //         // Add validation for child age inputs
            //         attachChildAgeValidation();
            //     }
            // }

            // // Function to attach event listeners to room inputs
            // function attachRoomInputListeners() {
            //     // Handle increment/decrement buttons for room traveler inputs only (not room count buttons)
            //     $('.increment-btn[data-target*="room_"][data-target*="_adults"], .increment-btn[data-target*="room_"][data-target*="_children"], .increment-btn[data-target*="room_"][data-target*="_babies"]').off('click').on('click', function(e) {
            //         e.preventDefault();
            //         const targetId = $(this).data('target');
            //         const input = $('#' + targetId);
            //         const currentValue = parseInt(input.val()) || 0;
            //         const maxValue = parseInt(input.attr('max'));
                    
            //         if (currentValue < maxValue) {
            //             input.val(currentValue + 1);
            //             // Clear error message
            //             const errorId = targetId.replace(/_/g, '-') + '-error';
            //             $('#' + errorId).text('');
                        
            //             // Handle children age inputs
            //             if (targetId.includes('_children')) {
            //                 const roomNumber = input.data('room');
            //                 const prefix = input.data('prefix') || '';
            //                 generateChildrenAgeInputs(roomNumber, currentValue + 1, prefix);
            //             }
            //         } else {
            //             // Show error message
            //             const fieldType = input.attr('name').includes('adults') ? 'adults' : 
            //                             input.attr('name').includes('children') ? 'children' : 'babies';
            //             const message = getErrorMessage(fieldType, maxValue);
            //             const errorId = targetId.replace(/_/g, '-') + '-error';
            //             $('#' + errorId).text(message);
            //         }
            //     });

            //     $('.decrement-btn[data-target*="room_"][data-target*="_adults"], .decrement-btn[data-target*="room_"][data-target*="_children"], .decrement-btn[data-target*="room_"][data-target*="_babies"]').off('click').on('click', function(e) {
            //         e.preventDefault();
            //         const targetId = $(this).data('target');
            //         const input = $('#' + targetId);
            //         const currentValue = parseInt(input.val()) || 0;
            //         const minValue = parseInt(input.attr('min'));
                    
            //         if (currentValue > minValue) {
            //             input.val(currentValue - 1);
            //             // Clear error message
            //             const errorId = targetId.replace(/_/g, '-') + '-error';
            //             $('#' + errorId).text('');
                        
            //             // Handle children age inputs
            //             if (targetId.includes('_children')) {
            //                 const roomNumber = input.data('room');
            //                 const prefix = input.data('prefix') || '';
            //                 generateChildrenAgeInputs(roomNumber, currentValue - 1, prefix);
            //             }
            //         }
            //     });

            //     // Handle direct input validation for room inputs
            //     $('.room-input').off('input change').on('input change', function(e) {
            //         e.preventDefault();
            //         const value = parseInt($(this).val());
            //         const max = parseInt($(this).attr('max'));
            //         const min = parseInt($(this).attr('min'));
            //         const errorId = $(this).attr('id').replace(/_/g, '-') + '-error';
                    
            //         $('#' + errorId).text('');
                    
            //         if (value > max) {
            //             $(this).val(max);
            //             const fieldType = $(this).attr('name').includes('adults') ? 'adults' : 
            //                             $(this).attr('name').includes('children') ? 'children' : 'babies';
            //             const message = getErrorMessage(fieldType, max);
            //             $('#' + errorId).text(message);
            //         } else if (value < min) {
            //             $(this).val(min);
            //         }
                    
            //         // Handle children age inputs for room inputs
            //         if ($(this).attr('name').includes('children')) {
            //             const roomNumber = $(this).data('room');
            //             const prefix = $(this).data('prefix') || '';
            //             const childrenCount = parseInt($(this).val()) || 0;
            //             generateChildrenAgeInputs(roomNumber, childrenCount, prefix);
            //         }
            //     });

            //     // Handle direct input validation for flight inputs (dropdown-item-input class)
            //     $('.dropdown-item-input').off('input change').on('input change', function(e) {
            //         const value = parseInt($(this).val());
            //         const max = parseInt($(this).attr('max'));
            //         const min = parseInt($(this).attr('min'));
            //         const inputId = $(this).attr('id');
            //         const errorId = inputId.replace(/_/g, '-') + '-error';
                    
            //         $('#' + errorId).text('');
                    
            //         if (value > max) {
            //             $(this).val(max);
            //             const fieldType = $(this).attr('name');
            //             let message = '';
            //             switch(fieldType) {
            //                 case 'adults':
            //                     message = '{{ __("dashboard.Maximum adults allowed is") }} ' + max;
            //                     break;
            //                 case 'children':
            //                     message = '{{ __("dashboard.Maximum children allowed is") }} ' + max;
            //                     break;
            //                 case 'infants':
            //                     message = '{{ __("dashboard.Maximum babies allowed is") }} ' + max;
            //                     break;
            //             }
            //             if (message) {
            //                 $('#' + errorId).text(message);
            //             }
            //         } else if (value < min) {
            //             $(this).val(min);
            //         }
            //     });

            //     // Handle children count changes to generate age inputs
            //     $('.children-count-input').off('input change').on('input change', function() {
            //         const roomNumber = $(this).data('room');
            //         const prefix = $(this).data('prefix') || '';
            //         const childrenCount = parseInt($(this).val()) || 0;
                    
            //         generateChildrenAgeInputs(roomNumber, childrenCount, prefix);
            //     });

            //     // Handle room deletion
            //     $('.delete-room-btn').off('click').on('click', function(e) {
            //         e.preventDefault();
            //         const roomNumber = $(this).data('room');
            //         const prefix = $(this).data('prefix');
            //         const isHotelRoom = prefix === 'hotel_';
            //         const roomsInputId = isHotelRoom ? '#hotel_rooms' : '#rooms';
            //         const containerId = isHotelRoom ? 'hotel-rooms-container' : 'rooms-container';
                    
            //         // Get current room count and decrease by 1
            //         const currentCount = parseInt($(roomsInputId).val()) || 1;
            //         if (currentCount > 1) {
            //             const newCount = currentCount - 1;
            //             $(roomsInputId).val(newCount);
                        
            //             // Regenerate room forms with new count
            //             generateRoomForms(containerId, newCount, prefix);
                        
            //             // Clear any error messages
            //             const errorId = isHotelRoom ? '#hotel-rooms-error' : '#rooms-error';
            //             $(errorId).text('');
            //         }
            //     });
            // }

            // // Helper function to get error messages
            // function getErrorMessage(fieldType, maxValue) {
            //     const messages = {
            //         'adults': '{{ __("dashboard.Maximum adults allowed is") }} ' + maxValue,
            //         'children': '{{ __("dashboard.Maximum children allowed is") }} ' + maxValue,
            //         'babies': '{{ __("dashboard.Maximum babies allowed is") }} ' + maxValue
            //     };
            //     return messages[fieldType] || '';
            // }

            // // Function to attach validation for child age inputs
            // function attachChildAgeValidation() {
            //     $('.child-age-select').off('change').on('change', function() {
            //         const value = $(this).val();
            //         const errorSpan = $(this).siblings('.child-age-error');
                    
            //         // Clear previous error
            //         errorSpan.text('');
            //         $(this).removeClass('is-invalid');
                    
            //         if (!value || value === '') {
            //             $(this).addClass('is-invalid');
            //             errorSpan.text('{{ __("dashboard.Please select an age") }}');
            //         }
            //     });
                
            //     // Also handle any remaining input fields (fallback)
            //     $('.child-age-input').off('input change').on('input change', function() {
            //         const value = parseInt($(this).val());
            //         const max = parseInt($(this).attr('max'));
            //         const min = parseInt($(this).attr('min'));
            //         const errorSpan = $(this).siblings('.child-age-error');
                    
            //         // Clear previous error
            //         errorSpan.text('');
            //         $(this).removeClass('is-invalid');
                    
            //         if ($(this).val() && (isNaN(value) || value < min || value > max)) {
            //             $(this).addClass('is-invalid');
            //             if (value < min) {
            //                 errorSpan.text('{{ __("dashboard.Minimum age is") }} ' + min);
            //             } else if (value > max) {
            //                 errorSpan.text('{{ __("dashboard.Maximum age is") }} ' + max);
            //             } else {
            //                 errorSpan.text('{{ __("dashboard.Please enter a valid age") }}');
            //             }
            //         }
            //     });
            // }

            // // Handle room count changes
            // $('#rooms').on('input change', function(e) {
            //     e.preventDefault();
            //     const roomCount = parseInt($(this).val()) || 1;
            //     const max = parseInt($(this).attr('max')) || 6;
            //     const min = parseInt($(this).attr('min')) || 1;
                
            //     if (roomCount > max) {
            //         $(this).val(max);
            //         $('#rooms-error').text('{{ __("dashboard.Maximum rooms allowed is") }} ' + max);
            //         generateRoomForms('rooms-container', max);
            //     } else if (roomCount < min) {
            //         $(this).val(min);
            //         $('#rooms-error').text('');
            //         generateRoomForms('rooms-container', min);
            //     } else {
            //         $('#rooms-error').text('');
            //         generateRoomForms('rooms-container', roomCount);
            //     }
            // });

            // $('#hotel_rooms').on('input change', function(e) {
            //     e.preventDefault();
            //     const roomCount = parseInt($(this).val()) || 1;
            //     const max = parseInt($(this).attr('max')) || 6;
            //     const min = parseInt($(this).attr('min')) || 1;
                
            //     if (roomCount > max) {
            //         $(this).val(max);
            //         $('#hotel-rooms-error').text('{{ __("dashboard.Maximum rooms allowed is") }} ' + max);
            //         generateRoomForms('hotel-rooms-container', max, 'hotel_');
            //     } else if (roomCount < min) {
            //         $(this).val(min);
            //         $('#hotel-rooms-error').text('');
            //         generateRoomForms('hotel-rooms-container', min, 'hotel_');
            //     } else {
            //         $('#hotel-rooms-error').text('');
            //         generateRoomForms('hotel-rooms-container', roomCount, 'hotel_');
            //     }
            // });

            // Initialize room functionality
            // function initializeRoomFunctionality() {
            //     // Initialize with default room forms
            //     generateRoomForms('rooms-container', 1);
            //     generateRoomForms('hotel-rooms-container', 1, 'hotel_');
                
            //     // Set up event handlers with more robust selectors
            //     $(document).off('click.roomCount').on('click.roomCount', '.increment-btn[data-target="rooms"], .decrement-btn[data-target="rooms"]', function(e) {
            //         e.preventDefault();
            //         e.stopPropagation();
            //         setTimeout(() => {
                        
                  
            //         const isIncrement = $(this).hasClass('increment-btn');
            //         const roomsInput = $('#rooms');
            //         const currentValue = parseInt(roomsInput.val()) || 1;
            //         const maxValue =  6;
            //         const minValue =  1;
                     
            //         let newValue = currentValue;
            //         if (isIncrement && currentValue < maxValue) {
            //             newValue = currentValue + 1;
            //         } else if (!isIncrement && currentValue > minValue) {
            //             newValue = currentValue - 1;
            //         }
            //         console.log("newValueee",newValue)
            //         if (newValue !== currentValue) {
            //             roomsInput.val(newValue).trigger('change');
            //         }
            //     }, 100);
                    
            //     });

            //     $(document).off('click.hotelRoomCount').on('click.hotelRoomCount', '.increment-btn[data-target="hotel_rooms"], .decrement-btn[data-target="hotel_rooms"]', function(e) {
            //         e.preventDefault();
            //         e.stopPropagation();
                    
            //         const isIncrement = $(this).hasClass('increment-btn');
            //         const roomsInput = $('#hotel_rooms');
            //         const currentValue = parseInt(roomsInput.val()) || 1;
            //         const maxValue = parseInt(roomsInput.attr('max')) || 6;
            //         const minValue = parseInt(roomsInput.attr('min')) || 1;
                    
            //         let newValue = currentValue;
            //         if (isIncrement && currentValue < maxValue) {
            //             newValue = currentValue + 1;
            //         } else if (!isIncrement && currentValue > minValue) {
            //             newValue = currentValue - 1;
            //         }
                    
            //         if (newValue !== currentValue) {
            //             roomsInput.val(newValue).trigger('change');
            //         }
            //     });
            // }
            // function initializeRoomFunctionality() {
            //     // Initialize with default room forms
            //     generateRoomForms('rooms-container', 1);
            //     generateRoomForms('hotel-rooms-container', 1, 'hotel_');

            //     // Remove ALL previous handlers completely before reattaching
            //     $(document).off('click.roomCount');
            //     $(document).off('click.hotelRoomCount');

            //     // Handle room count buttons with more specific selectors
            //     $(document).on('click.roomCount', '.increment-btn[data-target="rooms"]:not(.processed)', function(e) {
            //         e.preventDefault();
            //         e.stopImmediatePropagation();
                    
            //         // Mark as processed to prevent double handling
            //         $(this).addClass('processed');
                    
            //         const roomsInput = $('#rooms');
            //         const currentValue = parseInt(roomsInput.val())-1 || 1;
            //         const maxValue = 6;
                    
            //         if (currentValue < maxValue) {
            //             roomsInput.val(currentValue + 1).trigger('change');
            //         }
                    
            //         // Remove processed class after a short delay
            //         setTimeout(() => $(this).removeClass('processed'), 100);
            //     });

            //     $(document).on('click.roomCount', '.decrement-btn[data-target="rooms"]:not(.processed)', function(e) {
            //         e.preventDefault();
            //         e.stopImmediatePropagation();
                    
            //         // Mark as processed to prevent double handling
            //         $(this).addClass('processed');
                    
            //         const roomsInput = $('#rooms');
            //         const currentValue = parseInt(roomsInput.val())+1 || 1;
            //         const minValue = 1;
                    
            //         if (currentValue > minValue) {
            //             roomsInput.val(currentValue - 1).trigger('change');
            //         }
                    
            //         // Remove processed class after a short delay
            //         setTimeout(() => $(this).removeClass('processed'), 100);
            //     });

            //     // Handle hotel room count buttons with more specific selectors
            //     $(document).on('click.hotelRoomCount', '.increment-btn[data-target="hotel_rooms"]:not(.processed)', function(e) {
            //         e.preventDefault();
            //         e.stopImmediatePropagation();
                    
            //         // Mark as processed to prevent double handling
            //         $(this).addClass('processed');
                    
            //         const roomsInput = $('#hotel_rooms');
            //         const currentValue = parseInt(roomsInput.val())-1 || 1;
            //         const maxValue = 6;
                    
            //         if (currentValue < maxValue) {
            //             roomsInput.val(currentValue + 1).trigger('change');
            //         }
                    
            //         // Remove processed class after a short delay
            //         setTimeout(() => $(this).removeClass('processed'), 100);
            //     });

            //     $(document).on('click.hotelRoomCount', '.decrement-btn[data-target="hotel_rooms"]:not(.processed)', function(e) {
            //         e.preventDefault();
            //         e.stopImmediatePropagation();
                    
            //         // Mark as processed to prevent double handling
            //         $(this).addClass('processed');
                    
            //         const roomsInput = $('#hotel_rooms');
            //         const currentValue = parseInt(roomsInput.val())+1 || 1;
            //         const minValue = 1;
                    
            //         if (currentValue > minValue) {
            //             roomsInput.val(currentValue - 1).trigger('change');
            //         }
                    
            //         // Remove processed class after a short delay
            //         setTimeout(() => $(this).removeClass('processed'), 100);
            //     });
            // }

            // // Call initialization only once when the page loads
            // $(document).ready(function() {
            //     initializeRoomFunctionality();
            // });

            // Real-time validation for traveler inputs (for flight form only)
            // function validateTravelerInput(inputId, maxValue, errorMessage) {
            //     $('#' + inputId).on('input change', function(e) {
            //         e.preventDefault();
            //         var value = parseInt($(this).val());
            //         var max = parseInt($(this).attr('max'));
            //         var min = parseInt($(this).attr('min'));
                    
            //         // Clear any existing error messages first
            //         var errorSpanId = inputId.replace('_', '-') + '-error';
            //         $('#' + errorSpanId).html('');
                    
            //         if (value > max) {
            //             $(this).val(max);
            //             $('#' + errorSpanId).html(errorMessage + ' ' + max);
            //         } else if (value < min) {
            //             $(this).val(min);
            //         }
            //     });
            // }

            // // Flight form validation (only for flight form - plans and hotel use room-based inputs)
            // validateTravelerInput('flight_adults', 6, '{{ __("dashboard.Maximum adults allowed is") }}');
            // validateTravelerInput('flight_children', 4, '{{ __("dashboard.Maximum children allowed is") }}');
            // validateTravelerInput('flight_babies', 4, '{{ __("dashboard.Maximum babies allowed is") }}');

            $(document).ready(function() {
                let lang = "{{ app()->getLocale() }}";
                $('#ai-trip #searchableSelect').select2({
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

                $('#hotel-form #searchableSelectArrivalLocation').select2({
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

                $('#flight-form #searchableSelectArrivalCity').select2({
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

                $('#flight-form #searchableSelectDepartureCity').select2({
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
                        url: '/search-city',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            let lang = "{{ app()->getLocale() }}";
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.name_en,
                                        text: lang == 'ar' ? item.name_ar : item.name_en,
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

                $('#guest_nationality').select2({
                    placeholder: 'Search...',
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    //minimumInputLength: 3,
                    // ajax: {
                    //     url: '/search-airport',
                    //     dataType: 'json',
                    //     delay: 250, // Delay in ms for making API call
                    //     data: function(params) {
                    //         return {
                    //             city_name: params.term // The search term entered by the user
                    //         };
                    //     },
                    //     processResults: function(data) {
                    //         return {
                    //             results: data.map(function(item) {
                    //                 return {
                    //                     id: item.id,
                    //                     text: item.city + ' (' + item.airport_code +
                    //                         ') ',
                    //                 }; // Adjust based on API response structure
                    //             })
                    //         };
                    //     },
                    //     cache: true
                    // }
                });

                $('#guest_nationality2').select2({
                    placeholder: 'Search...',
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                });

                $('#guest_nationality3').select2({
                    placeholder: 'Search...',
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                });
            });
            $(document).ready(function() {
                // Handle radio button changes
                    $('input[name="type"]').change(function() {
                        const value = $(this).val();
                        
                        // Hide all sections first
                        $('.radio-section').hide();
                        
                        // Show the selected section
                        $(`.radio${value}`).show();
                        
                        // Initialize the travelers input for the shown section
                        // if(value == 1) {
                        //     // Full version with rooms
                        //     generateRoomForms('rooms-container', $('#rooms').val() || 1, '');
                        // } 
                        // else if(value == 2) {
                        //     // Hotel version - rooms only
                        //     $('#hotel_rooms').trigger('change');
                        // }
                        // else if(value == 3) {
                        //     // Flight version - passengers only
                        //     const childrenCount = parseInt($('#flight_children').val()) || 0;
                        //     generateChildrenAgeInputs('flight_');
                        // }
                    });

                    // Initialize with first radio selected
                    $('input[name="type"]:first').trigger('change');
                });
            });
    </script>

    <script>
        $(document).ready(function() {
            // Set default dates: From = today + 2 days, To = From + 1 day
            function setDefaultDates() {
                const today = new Date();
                
                // From date: today + 2 days
                const fromDate = new Date(today);
                fromDate.setDate(today.getDate() + 2);
                
                // To date: from date + 1 day  
                const toDate = new Date(fromDate);
                toDate.setDate(fromDate.getDate() + 1);
                
                // Format dates as YYYY-MM-DD (HTML5 date input format)
                function formatDate(date) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${year}-${month}-${day}`;
                }
                
                const fromDateStr = formatDate(fromDate);
                const toDateStr = formatDate(toDate);
                
                // Set default dates for all date inputs
                // Flight & Hotel Trip dates
                $('#startDate').val(fromDateStr);
                $('#endDate').val(toDateStr);
                
                // Hotel only dates
                // $('#hotel_checkin').val(fromDateStr);
                // $('#hotel_checkout').val(toDateStr);
                
                // // Flight only dates (if they exist)
                // $('#flight_start_date').val(fromDateStr);
                // $('#flight_end_date').val(toDateStr);
            }
            
            // Set default dates when page loads
            setDefaultDates();
            
            // Load saved form data from localStorage
            loadFormData();
        });
    </script>

    <script>
        $(document).ready(function() {
            // LocalStorage keys for the three trip types
            const STORAGE_KEYS = {
                FLIGHT_HOTEL: 'ai_trip_flight_hotel_form',
                HOTEL_ONLY: 'ai_trip_hotel_only_form', 
                FLIGHT_ONLY: 'ai_trip_flight_only_form'
            };

            // Function to save form data to localStorage
            function saveFormData() {
                const tripType = $('input[name="type"]:checked').val();
                let formData = {};
                
                if (tripType === '1') { // Flight + Hotel
                    // Get Select2 data for origin
                    const originSelect = $('#searchableSelect');
                    const originData = originSelect.select2('data')[0];
                    
                    // Get travel interests (checkboxes)
                    const selectedInterests = [];
                    $('#ai-trip input[name="plans[]"]:checked').each(function() {
                        selectedInterests.push($(this).val());
                    });
                    
                    // Get destination preference
                    const destinationPreference = $('#country_id').val();
                    
                    // Get traveler summary from dropdown input
                    const travelerSummary = $('#ai-trip #travelers').val();
                    
                    // Get room passenger data
                    const roomCount = parseInt($('#rooms').val()) || 1;
                    
                    // Calculate actual traveler summary from roomsData for accuracy
                    let calculatedTravelerSummary = 0;
                    for (let i = 1; i <= roomCount; i++) {
                        calculatedTravelerSummary += (parseInt($(`#room_${i}_adults`).val()) || (i === 1 ? 1 : 0));
                        calculatedTravelerSummary += (parseInt($(`#room_${i}_children`).val()) || 0);
                        calculatedTravelerSummary += (parseInt($(`#room_${i}_babies`).val()) || 0);
                    }
                    
                    const roomsData = {};
                    for (let i = 1; i <= roomCount; i++) {
                        roomsData[`room_${i}_adults`] = parseInt($(`#room_${i}_adults`).val()) || (i === 1 ? 1 : 0);
                        roomsData[`room_${i}_children`] = parseInt($(`#room_${i}_children`).val()) || 0;
                        roomsData[`room_${i}_babies`] = parseInt($(`#room_${i}_babies`).val()) || 0;
                        
                        // Save children ages if any
                        const childAges = [];
                        $(`#ai-trip #room_${i}_children_ages select`).each(function() {
                            if ($(this).val()) {
                                childAges.push($(this).val());
                            }
                        });
                        if (childAges.length > 0) {
                            roomsData[`room_${i}_child_ages`] = childAges;
                        }
                    }
                    
                    formData = {
                        guest_nationality: $('#ai-trip #guest_nationality').val(),
                        origin: originData ? { id: originData.id, text: originData.text } : null,
                        startDate: $('#ai-trip #startDate').val(),
                        endDate: $('#ai-trip #endDate').val(),
                        rooms: $('#ai-trip #rooms').val(),
                        budget: $('#ai-trip input[name="budget"]:checked').val(),
                        travel_interests: selectedInterests,
                        destination_preference: destinationPreference,
                        traveler_summary: calculatedTravelerSummary.toString(),
                        roomsData: roomsData
                    };
                    localStorage.setItem(STORAGE_KEYS.FLIGHT_HOTEL, JSON.stringify(formData));
                    
                } else if (tripType === '2') { // Hotel Only
                    // Get Select2 data for hotel destination
                    const destinationSelect = $('#hotel-form #searchableSelectArrivalLocation');
                    const destinationData = destinationSelect.select2('data')[0];
                    
                    // Get traveler summary from hotel dropdown input
                    const hotelTravelerSummary = $('#hotel-form #hotel_travelers').val();
                    
                    // Get hotel room passenger data
                    const hotelRoomCount = parseInt($('#hotel-form #hotel_rooms').val()) || 1;
                    
                    // Calculate actual traveler summary from hotelRoomsData for accuracy
                    let calculatedHotelTravelerSummary = 0;
                    for (let i = 1; i <= hotelRoomCount; i++) {
                        calculatedHotelTravelerSummary += (parseInt($(`#hotel-form #hotel_room_${i}_adults`).val()) || (i === 1 ? 1 : 0));
                        calculatedHotelTravelerSummary += (parseInt($(`#hotel-form #hotel_room_${i}_children`).val()) || 0);
                        calculatedHotelTravelerSummary += (parseInt($(`#hotel-form #hotel_room_${i}_babies`).val()) || 0);
                    }
                    
                    const hotelRoomsData = {};
                    for (let i = 1; i <= hotelRoomCount; i++) {
                        hotelRoomsData[`hotel_room_${i}_adults`] = parseInt($(`#hotel-form #hotel_room_${i}_adults`).val()) || (i === 1 ? 1 : 0);
                        hotelRoomsData[`hotel_room_${i}_children`] = parseInt($(`#hotel-form #hotel_room_${i}_children`).val()) || 0;
                        hotelRoomsData[`hotel_room_${i}_babies`] = parseInt($(`#hotel-form #hotel_room_${i}_babies`).val()) || 0;
                        
                        // Save children ages if any
                        const childAges = [];
                        $(`#hotel-form #hotel_room_${i}_children_ages select`).each(function() {
                            if ($(this).val()) {
                                childAges.push($(this).val());
                            }
                        });
                        if (childAges.length > 0) {
                            hotelRoomsData[`hotel_room_${i}_child_ages`] = childAges;
                        }
                    }
                    
                    formData = {
                        guest_nationality: $('#hotel-form #guest_nationality2').val(),
                        hotel_destination: destinationData ? { id: destinationData.id, text: destinationData.text } : null,
                        hotel_checkin: $('#hotel-form #hotel_checkin').val(),
                        hotel_checkout: $('#hotel-form #hotel_checkout').val(),
                        hotel_rooms: $('#hotel-form #hotel_rooms').val(),
                        hotel_budget: $('#hotel-form input[name="budget"]:checked').val(),
                        hotel_traveler_summary: calculatedHotelTravelerSummary.toString(),
                        hotelRoomsData: hotelRoomsData
                    };
                    localStorage.setItem(STORAGE_KEYS.HOTEL_ONLY, JSON.stringify(formData));
                    
                } else if (tripType === '3') { // Flight Only
                    // Get Select2 data for departure and arrival cities
                    const departureSelect = $('#flight-form #searchableSelectDepartureCity');
                    const arrivalSelect = $('#flight-form #searchableSelectArrivalCity');
                    const departureData = departureSelect.select2('data')[0];
                    const arrivalData = arrivalSelect.select2('data')[0];
                    
                    // Get traveler summary from flight dropdown input
                    let flightTravelerSummary = $('#flight-form #flight_travelers').val() 
                    
                    // Calculate actual traveler summary from flight passenger data for accuracy
                    const calculatedFlightTravelerSummary = (parseInt($('#flight-form #flight_adults').val()) || 0) + 
                                                           (parseInt($('#flight-form #flight_children').val()) || 0) + 
                                                           (parseInt($('#flight-form #flight_babies').val()) || 0);
                    
                    // Try alternative selectors if the first ones don't work
                    // if (!flightTravelerSummary) {
                    //     flightTravelerSummary = $('#flight-form .radio3 input[placeholder*="Travelers"]').val() || $('#flight-form .radio3 input[placeholder*="travelers"]').val();
                    // }
                    
                    // Get children ages if any
                    const flightChildAges = [];
                    $('#flight-form #flight_children_ages select').each(function() {
                        if ($(this).val()) {
                            flightChildAges.push($(this).val());
                        }
                    });
                    
                    // Try alternative selectors if first one didn't find anything
                    if (flightChildAges.length === 0) {
                        $('#flight-form .flight-children-ages-container select, [id*="flight"][id*="children_ages"] select').each(function() {
                            if ($(this).val()) {
                                flightChildAges.push($(this).val());
                            }
                        });
                    }
                    
                    // Get passenger counts
                    const flightAdults = $('#flight-form #flight_adults').val();
                    const flightChildren = $('#flight-form #flight_children').val();
                    const flightBabies = $('#flight-form #flight_babies').val();

                    formData = {
                        guest_nationality: $('#flight-form #guest_nationality3').val(),
                        departure_city: departureData ? { id: departureData.id, text: departureData.text } : null,
                        arrival_city: arrivalData ? { id: arrivalData.id, text: arrivalData.text } : null,
                        flight_start_date: $('#flight-form #flight_start_date').val(),
                        flight_end_date: $('#flight-form #flight_end_date').val(),
                        journey_type: $('#flight-form #journey_type').val(),
                        flight_budget: $('#flight-form input[name="budget"]:checked').val(),
                        flight_adults: flightAdults,
                        flight_children: flightChildren,
                        flight_babies: flightBabies,
                        flight_child_ages: flightChildAges.length > 0 ? flightChildAges : null,
                        flight_traveler_summary: calculatedFlightTravelerSummary.toString()
                    };
                    localStorage.setItem(STORAGE_KEYS.FLIGHT_ONLY, JSON.stringify(formData));
                }
            }

            // Function to load form data from localStorage
            function loadFormData() {
                // Load Flight + Hotel data
                const flightHotelData = localStorage.getItem(STORAGE_KEYS.FLIGHT_HOTEL);
                if (flightHotelData) {
                    const data = JSON.parse(flightHotelData);
                    
                    // Restore nationality
                    if (data.guest_nationality) {
                        $('#ai-trip #guest_nationality').val(data.guest_nationality).trigger('change');
                    }
                    
                    // Restore origin Select2 with proper option
                    if (data.origin && data.origin.id && data.origin.text) {
                        const originSelect = $('#ai-trip #searchableSelect');
                        const newOption = new Option(data.origin.text, data.origin.id, true, true);
                        originSelect.append(newOption).trigger('change');
                    }
                    
                    // Restore dates
                    if (data.startDate) {
                        $('#ai-trip #startDate').val(data.startDate);
                    }
                    if (data.endDate) {
                        $('#ai-trip #endDate').val(data.endDate);
                    }
                    
                    // Restore rooms count
                    if (data.rooms) {
                        $('#ai-trip #rooms').val(data.rooms);
                    }
                    
                    // Restore budget
                    if (data.budget) {
                        $(`#ai-trip input[name="budget"][value="${data.budget}"]`).prop('checked', true);
                    }
                    
                    // Restore travel interests
                    if (data.travel_interests && data.travel_interests.length > 0) {
                        data.travel_interests.forEach(interestId => {
                            $(`#ai-trip input[name="plans[]"][value="${interestId}"]`).prop('checked', true);
                        });
                    }
                    
                    // Restore destination preference
                    if (data.destination_preference) {
                        const countrySelect = $('#ai-trip #country_id');
                        if (countrySelect.length > 0) {
                            countrySelect.val(data.destination_preference).trigger('change');
                        }
                    }
                    
                    // Restore traveler summary
                    if (data.traveler_summary) {
                        $('#ai-trip .dropdown-input[placeholder*="Travelers"]').val(data.traveler_summary);
                        $('#ai-trip .dropdown-input[placeholder*="travelers"]').val(data.traveler_summary);
                    }
                    
                    // Restore room passenger data using new setRoomData function
                    if (data.roomsData) {
                        setRoomData(data.roomsData, '');
                    }
                    
                    // Update traveler summary after loading Flight + Hotel data
                    setTimeout(function() {
                        updateFlightHotelTravelerSummary();
                    }, 1500); // Delay to ensure all room forms and ages are loaded
                }

                // Load Hotel Only data
                const hotelOnlyData = localStorage.getItem(STORAGE_KEYS.HOTEL_ONLY);
                if (hotelOnlyData) {
                    const data = JSON.parse(hotelOnlyData);
                    
                    // Restore nationality
                    if (data.guest_nationality) {
                        $('#hotel-form #guest_nationality2').val(data.guest_nationality).trigger('change');
                    }
                    
                    // Restore hotel destination Select2 with proper option
                    if (data.hotel_destination && data.hotel_destination.id && data.hotel_destination.text) {
                        const destinationSelect = $('#hotel-form #searchableSelectArrivalLocation');
                        const newOption = new Option(data.hotel_destination.text, data.hotel_destination.id, true, true);
                        destinationSelect.append(newOption).trigger('change');
                    }
                    
                    // Restore dates
                    if (data.hotel_checkin) $('#hotel-form #hotel_checkin').val(data.hotel_checkin);
                    if (data.hotel_checkout) $('#hotel-form #hotel_checkout').val(data.hotel_checkout);
                    
                    // Restore hotel rooms count
                    if (data.hotel_rooms) $('#hotel-form #hotel_rooms').val(data.hotel_rooms);
                    
                    // Restore budget
                    if (data.hotel_budget) {
                        $(`#hotel-form input[name="budget"][value="${data.hotel_budget}"]`).prop('checked', true);
                    }
                    
                    // Restore hotel traveler summary
                    if (data.hotel_traveler_summary) {
                        $('#hotel-form .radio2 .dropdown-input[placeholder*="Travelers"]').val(data.hotel_traveler_summary);
                        $('#hotel-form .radio2 .dropdown-input[placeholder*="travelers"]').val(data.hotel_traveler_summary);
                    }
                    
                    // Restore hotel room passenger data using new setRoomData function
                    if (data.hotelRoomsData) {
                        setRoomData(data.hotelRoomsData, 'hotel_');
                    }
                    
                    // Update traveler summary after loading Hotel Only data
                    setTimeout(function() {
                        updateHotelTravelerSummary();
                    }, 1500); // Delay to ensure all room forms and ages are loaded
                }

                // Load Flight Only data
                const flightOnlyData = localStorage.getItem(STORAGE_KEYS.FLIGHT_ONLY);
                if (flightOnlyData) {
                    const data = JSON.parse(flightOnlyData);
                    
                    // Restore nationality
                    if (data.guest_nationality) {
                        $('#flight-form #guest_nationality3').val(data.guest_nationality).trigger('change');
                    }
                    
                    // Restore departure city Select2 with proper option
                    if (data.departure_city && data.departure_city.id && data.departure_city.text) {
                        const departureSelect = $('#flight-form #searchableSelectDepartureCity');
                        const newOption = new Option(data.departure_city.text, data.departure_city.id, true, true);
                        departureSelect.append(newOption).trigger('change');
                    }
                    
                    // Restore arrival city Select2 with proper option
                    if (data.arrival_city && data.arrival_city.id && data.arrival_city.text) {
                        const arrivalSelect = $('#flight-form #searchableSelectArrivalCity');
                        const newOption = new Option(data.arrival_city.text, data.arrival_city.id, true, true);
                        arrivalSelect.append(newOption).trigger('change');
                    }
                    
                    // Restore dates
                    if (data.flight_start_date) {
                        $('#flight-form #flight_start_date').val(data.flight_start_date);
                    }
                    if (data.flight_end_date) {
                        $('#flight-form #flight_end_date').val(data.flight_end_date);
                    }
                    
                    // Restore journey type
                    if (data.journey_type) {
                        $('#flight-form #journey_type').val(data.journey_type).trigger('change');
                    }
                    
                    // Restore passenger counts
                    if (data.flight_adults) {
                        $('#flight-form #flight_adults').val(data.flight_adults);
                    }
                    if (data.flight_children) {
                        $('#flight-form #flight_children').val(data.flight_children);
                        $('#flight-form #flight_children').trigger('change'); // Trigger to generate age fields
                    }
                    if (data.flight_babies) {
                        $('#flight-form #flight_babies').val(data.flight_babies);
                    }
                    
                    // Restore budget
                    if (data.flight_budget) {
                        $(`#flight-form input[name="budget"][value="${data.flight_budget}"]`).prop('checked', true);
                    }
                    
                    // Restore flight traveler summary
                    if (data.flight_traveler_summary) {
                        let radio3TravelerInputs = $('#flight-form .radio3 .dropdown-input[placeholder*="Travelers"], .radio3 .dropdown-input[placeholder*="travelers"]');
                        
                        if (radio3TravelerInputs.length > 0) {
                            radio3TravelerInputs.val(data.flight_traveler_summary);
                        } else {
                            // Try alternative selectors
                            radio3TravelerInputs = $('#flight-form .radio3 input[placeholder*="Travelers"], .radio3 input[placeholder*="travelers"]');
                            radio3TravelerInputs.val(data.flight_traveler_summary);
                        }
                        
                        // Also try generic selector as final fallback
                        if (radio3TravelerInputs.length === 0) {
                            $('#flight-form .dropdown-input[placeholder*="Travelers"], .dropdown-input[placeholder*="travelers"]').val(data.flight_traveler_summary);
                        }
                    }
                    
                    // Restore children ages
                    if (data.flight_child_ages && data.flight_child_ages.length > 0) {
                        // Try immediate restoration first
                        let ageSelects = $('#flight_children_ages select, [id*="flight"][id*="children_ages"] select, [id*="flight_children_ages"] select');
                        
                        if (ageSelects.length > 0) {
                            ageSelects.each(function(index) {
                                if (data.flight_child_ages[index]) {
                                    $(this).val(data.flight_child_ages[index]);
                                }
                            });
                        }
                        
                        // Also try with timeout for dynamically generated elements
                        setTimeout(() => {
                            ageSelects = $('#flight_children_ages select, [id*="flight"][id*="children_ages"] select, [id*="flight_children_ages"] select');
                            
                            ageSelects.each(function(index) {
                                if (data.flight_child_ages[index]) {
                                    $(this).val(data.flight_child_ages[index]);
                                }
                            });
                        }, 1000);
                    }
                    
                    // Update traveler summary after loading Flight Only data
                    setTimeout(function() {
                        updateFlightTravelerSummary();
                    }, 1500); // Delay to ensure all fields are loaded
                }
            }
            
            // Function to update traveler summary for Flight + Hotel (rooms version)
            function updateFlightHotelTravelerSummary() {
                const roomCount = parseInt($('#ai-trip #rooms').val()) || 1;
                let totalAdults = 0;
                let totalChildren = 0;
                let totalBabies = 0;
                
                for (let i = 1; i <= roomCount; i++) {
                    totalAdults += parseInt($(`#ai-trip #room_${i}_adults`).val()) || 0;
                    totalChildren += parseInt($(`#ai-trip #room_${i}_children`).val()) || 0;
                    totalBabies += parseInt($(`#ai-trip #room_${i}_babies`).val()) || 0;
                }
                
                const totalTravelers = totalAdults + totalChildren + totalBabies;
                $('#ai-trip #travelers').val(totalTravelers);
            }
            
            // Function to update traveler summary for Hotel Only (rooms version)
            function updateHotelTravelerSummary() {
                const roomCount = parseInt($('#hotel-form #hotel_rooms').val()) || 1;
                let totalAdults = 0;
                let totalChildren = 0;
                let totalBabies = 0;
                
                for (let i = 1; i <= roomCount; i++) {
                    totalAdults += parseInt($(`#hotel-form #hotel_room_${i}_adults`).val()) || 0;
                    totalChildren += parseInt($(`#hotel-form #hotel_room_${i}_children`).val()) || 0;
                    totalBabies += parseInt($(`#hotel-form #hotel_room_${i}_babies`).val()) || 0;
                }
                
                const totalTravelers = totalAdults + totalChildren + totalBabies;
                $('#hotel-form #hotel_travelers').val(totalTravelers);
            }
            
            // Function to update traveler summary for Flight Only (passengers version)
            function updateFlightTravelerSummary() {
                const adults = parseInt($('#flight-form #flight_adults').val()) || 0;
                const children = parseInt($('#flight-form #flight_children').val()) || 0;
                const babies = parseInt($('#flight-form #flight_babies').val()) || 0;
                
                const totalTravelers = adults + children + babies;
                $('#flight-form #flight_travelers').val(totalTravelers || '{{ __("dashboard.Travelers") }}');
            }

            // Save form data whenever inputs change
            function bindFormChangeEvents() {
                // Flight + Hotel form fields
                $('#ai-trip #guest_nationality, #ai-trip #searchableSelect, #ai-trip #startDate, #ai-trip #endDate, #ai-trip #rooms, #ai-trip #travelers').on('change input', function() {
                    if ($('input[name="type"]:checked').val() === '1') {
                        // Update traveler summary if rooms count changed
                        if ($(this).attr('id') === 'rooms') {
                            setTimeout(function() {
                                updateFlightHotelTravelerSummary();
                            }, 100); // Small delay to allow room forms to be generated
                        }
                        saveFormData();
                    }
                });
                
                // Room passenger fields for Flight + Hotel
                $(document).on('change input', '#ai-trip [id^="room_"][id$="_adults"], #ai-trip [id^="room_"][id$="_children"], #ai-trip [id^="room_"][id$="_babies"]', function() {
                    if ($('input[name="type"]:checked').val() === '1') {
                        updateFlightHotelTravelerSummary(); // Update traveler summary
                        saveFormData();
                    }
                });
                
                // Room children ages for Flight + Hotel  
                $(document).on('change', '#ai-trip [id*="room_"][id*="children_ages"] select', function() {
                    if ($('input[name="type"]:checked').val() === '1') {
                        saveFormData();
                    }
                });
                
                // Hotel Only form fields
                $('#hotel-form #guest_nationality2, #hotel-form #searchableSelectArrivalLocation, #hotel-form #hotel_checkin, #hotel-form #hotel_checkout, #hotel-form #hotel_rooms, #hotel-form #hotel_travelers').on('change input', function() {
                    if ($('input[name="type"]:checked').val() === '2') {
                        // Update traveler summary if hotel rooms count changed
                        if ($(this).attr('id') === 'hotel_rooms') {
                            setTimeout(function() {
                                updateHotelTravelerSummary();
                            }, 100); // Small delay to allow room forms to be generated
                        }
                        saveFormData();
                    }
                });
                
                // Hotel room passenger fields
                $(document).on('change input', '#hotel-form [id^="hotel_room_"][id$="_adults"], #hotel-form [id^="hotel_room_"][id$="_children"], #hotel-form [id^="hotel_room_"][id$="_babies"]', function() {
                    if ($('input[name="type"]:checked').val() === '2') {
                        updateHotelTravelerSummary(); // Update traveler summary
                        saveFormData();
                    }
                });
                
                // Hotel room children ages
                $(document).on('change', '#hotel-form [id*="hotel_room_"][id*="children_ages"] select', function() {
                    if ($('input[name="type"]:checked').val() === '2') {
                        saveFormData();
                    }
                });
                
                // Flight Only form fields
                $('#flight-form #guest_nationality3, #flight-form #searchableSelectDepartureCity, #flight-form #searchableSelectArrivalCity, #flight-form #flight_start_date, #flight-form #flight_end_date, #flight-form #journey_type').on('change input', function() {
                    if ($('input[name="type"]:checked').val() === '3') {
                        saveFormData();
                    }
                });
                
                // Flight passenger fields
                $('#flight-form #flight_adults, #flight-form #flight_children, #flight-form #flight_babies, #flight-form #flight_travelers').on('change input', function() {
                    if ($('input[name="type"]:checked').val() === '3') {
                        // Update traveler summary if passenger counts changed
                        if ($(this).attr('id') === 'flight_adults' || $(this).attr('id') === 'flight_children' || $(this).attr('id') === 'flight_babies') {
                            updateFlightTravelerSummary();
                        }
                        saveFormData();
                    }
                });
                
                // Flight children ages
                $(document).on('change', '#flight-form #flight_children_ages select', function() {
                    if ($('input[name="type"]:checked').val() === '3') {
                        saveFormData();
                    }
                });
                
                // Budget radio buttons for all trip types
                $('#ai-trip input[name="budget"], #hotel-form input[name="budget"], #flight-form input[name="budget"]').on('change', function() {
                    saveFormData();
                });
                
                // Travel interests checkboxes for Flight + Hotel
                $(document).on('change', '#ai-trip input[name="plans[]"]', function() {
                    if ($('input[name="type"]:checked').val() === '1') {
                        saveFormData();
                    }
                });
                
                // Destination preference dropdown for Flight + Hotel
                $(document).on('change', '#ai-trip #country_id', function() {
                    if ($('input[name="type"]:checked').val() === '1') {
                        saveFormData();
                    }
                });
                
                // Traveler summary dropdown inputs for all trip types
                $(document).on('input change', '#ai-trip .dropdown-input[placeholder*="Travelers"], #ai-trip .dropdown-input[placeholder*="travelers"], #hotel-form .dropdown-input[placeholder*="Travelers"], #hotel-form .dropdown-input[placeholder*="travelers"], #flight-form .dropdown-input[placeholder*="Travelers"], #flight-form .dropdown-input[placeholder*="travelers"]', function() {
                    saveFormData(); // Save for whichever trip type is active
                });
                
                // Trip type selection
                $('input[name="type"]').on('change', function() {
                    loadFormData(); // Load data when switching trip types
                    
                    // Update traveler summaries after trip type change
                    const tripType = $(this).val();
                    setTimeout(function() {
                        if (tripType === '1') {
                            updateFlightHotelTravelerSummary();
                        } else if (tripType === '2') {
                            updateHotelTravelerSummary();
                        } else if (tripType === '3') {
                            updateFlightTravelerSummary();
                        }
                    }, 2000); // Longer delay to ensure forms are fully loaded
                });
            }

            // Initialize form change events
            bindFormChangeEvents();
            
            // Initialize traveler summaries on page load
            setTimeout(function() {
                const initialTripType = $('input[name="type"]:checked').val();
                if (initialTripType === '1') {
                    updateFlightHotelTravelerSummary();
                } else if (initialTripType === '2') {
                    updateHotelTravelerSummary();
                } else if (initialTripType === '3') {
                    updateFlightTravelerSummary();
                }
            }, 2500); // Delay to ensure everything is loaded from localStorage
        });
    </script>

    <script>
        // Function to set room data programmatically from localStorage or provided data
        function setRoomData(roomData = null, prefix = '') {
            // If no roomData provided, try to get it from localStorage
            if (!roomData) {
                const tripType = $('input[name="type"]:checked').val();
                let storageKey;
                
                if (prefix === 'hotel_') {
                    storageKey = 'ai_trip_hotel_only_form';
                } else if (tripType === '1') {
                    storageKey = 'ai_trip_flight_hotel_form';
                } else {
                    return; // No applicable storage for flight-only
                }
                
                const storedData = localStorage.getItem(storageKey);
                if (storedData) {
                    const parsedData = JSON.parse(storedData);
                    roomData = prefix === 'hotel_' ? parsedData.hotelRoomsData : parsedData.roomsData;
                }
                
                if (!roomData) {
                    return;
                }
            }
            
            // Transform hotel room data keys from hotel_room_X_ to room_X_ for component compatibility
            if (prefix === 'hotel_' && roomData) {
                const transformedData = {};
                for (const [key, value] of Object.entries(roomData)) {
                    const transformedKey = key.replace(/^hotel_room_/, 'room_');
                    transformedData[transformedKey] = value;
                }
                roomData = transformedData;
            }
            
            // Determine room count from the data
            const roomNumbers = Object.keys(roomData)
                .filter(key => key.includes('_adults'))
                .map(key => parseInt(key.match(/(\d+)/)[1]))
                .filter(num => !isNaN(num));
            
            const roomCount = Math.max(...roomNumbers) || 1;
            
            // Set the room count input
            const roomsInput = $(`#${prefix}rooms`);
            if (roomsInput.length) {
                roomsInput.val(roomCount);
            }
            
            // Call generateRoomForms if it exists (it should be available from the component)
            const containerId = `${prefix}rooms-container`;
            if (window.generateRoomForms && document.getElementById(containerId)) {
                window.generateRoomForms(containerId, roomCount, prefix, roomData);
            } else {
                // Fallback: trigger change event to regenerate rooms
                roomsInput.trigger('change');
            }
        }

        
        
        // Function to load room data from localStorage
        function loadRoomDataFromLocalStorage(prefix = '') {
            setRoomData(null, prefix); // Will automatically load from localStorage
        }
        
        // Function to get current room data from the component
        function getRoomData(prefix = '') {
            const roomsInput = $(`#${prefix}rooms`);
            if (!roomsInput.length) return {};
            
            const roomCount = parseInt(roomsInput.val()) || 1;
            const roomData = {};
            
            for (let i = 1; i <= roomCount; i++) {
                const adultsInput = $(`#${prefix}room_${i}_adults`);
                const childrenInput = $(`#${prefix}room_${i}_children`);
                const babiesInput = $(`#${prefix}room_${i}_babies`);
                
                if (adultsInput.length) {
                    roomData[`room_${i}_adults`] = parseInt(adultsInput.val()) || 1;
                    roomData[`room_${i}_children`] = parseInt(childrenInput.val()) || 0;
                    roomData[`room_${i}_babies`] = parseInt(babiesInput.val()) || 0;
                    
                    // Get child ages if any
                    const childrenCount = roomData[`room_${i}_children`];
                    if (childrenCount > 0) {
                        const childAges = [];
                        for (let j = 1; j <= childrenCount; j++) {
                            const ageSelect = $(`#${prefix}room_${i}_child_${j}_age`);
                            if (ageSelect.length && ageSelect.val()) {
                                childAges.push(parseInt(ageSelect.val()));
                            }
                        }
                        if (childAges.length > 0) {
                            roomData[`room_${i}_child_ages`] = childAges;
                        }
                    }
                }
            }
            
            return roomData;
        }
        
        // Example usage:
        // const currentRoomData = getRoomData(''); // Get Flight+Hotel data
        function logCurrentRoomData() {
            const flightHotelData = getRoomData(''); // Flight+Hotel component
            const hotelData = getRoomData('hotel_'); // Hotel Only component
            
            // Also show in alert for easy viewing
            alert('Check the console for detailed room data!\n\nFlight+Hotel: ' + Object.keys(flightHotelData).length + ' room fields\nHotel Only: ' + Object.keys(hotelData).length + ' room fields');
        }
        
        // Helper functions for manual localStorage integration
        function loadFlightHotelRoomsFromStorage() {
            loadRoomDataFromLocalStorage(''); // Flight+Hotel uses empty prefix
        }
        
        function loadHotelOnlyRoomsFromStorage() {
            loadRoomDataFromLocalStorage('hotel_'); // Hotel Only uses 'hotel_' prefix
        }
        
        function loadCurrentActiveRoomsFromStorage() {
            const tripType = $('input[name="type"]:checked').val();
            if (tripType === '1') {
                loadFlightHotelRoomsFromStorage();
            } else if (tripType === '2') {
                loadHotelOnlyRoomsFromStorage();
            }
        }
    </script>


    <script>
        $(document).ready(function() {
            function toggleOneWayDiv() {
                const selectedValue = $('#flight-form #journey_type').val();
                if (selectedValue === '1') {
                    $('.one_way').hide();
                } else {
                    $('.one_way').show();
                }
            }
            toggleOneWayDiv();
            $('#flight-form #journey_type').on('change', function() {
                toggleOneWayDiv();
            });
        });
    </script>
@endsection
