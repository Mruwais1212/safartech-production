@extends('website.layout')
@section('title', __('dashboard.passenger_information'))

@php
    // Helper function to get saved form value using Laravel's old() helper
    function getSavedValue($field, $index = null, $default = '') {
        if ($index !== null) {
            return old($field.'.'.$index, $default);
        }
        return old($field, $default);
    }
@endphp

@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/info-banner.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-12 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.passenger_information') }}
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
                <form action="/passenger-information" class="steps-form" method="POST">
                    @csrf
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s"
                                    style="max-width: 100%;">
                                    <h4 class="mb-3 section-title mt-4 text-start">{{ __('dashboard.passenger_information') }}</h4>
                                    <p>{{ __('dashboard.Passenger information must match government-issued') }}</p>
                                    
                                    <!-- Name Requirements Notice -->
                                    <div class="alert alert-info text-start mb-4">
                                        <h6 class="fw-bold mb-2">{{ __('dashboard.Guest Name Requirements') }}:</h6>
                                        <ul class="mb-0 small">
                                            <li>{{ __('dashboard.Names must match your passport exactly') }}</li>
                                            <li>{{ __('dashboard.Minimum 2 characters required') }}</li>
                                            <li>{{ __('dashboard.Only letters, spaces, hyphens, apostrophes, and dots allowed') }}</li>
                                            <li>{{ __('dashboard.Numbers and special characters are not permitted') }}</li>
                                            <li>{{ __('dashboard.Each guest must have a unique first name') }}</li>
                                        </ul>
                                    </div>
                                    
                                    <!-- Age and Passport Requirements Notice -->
                                    <div class="alert alert-warning text-start mb-4">
                                        <h6 class="fw-bold mb-2">{{ __('dashboard.Important Requirements') }}:</h6>
                                        <ul class="mb-0 small">
                                            <li><strong>{{ __('dashboard.Child Age') }}:</strong> {{ __('dashboard.child_age_validation') }}</li>
                                            <li><strong>{{ __('dashboard.Passport Expiry') }}:</strong> {{ __('dashboard.passport_expiry_min_months') }}</li>
                                        </ul>
                                    </div>
                                    
                                    <!-- Display Validation Errors -->
                                    @if($errors->any())
                                        <div class="alert alert-danger text-start mb-4">
                                            <h6 class="fw-bold mb-2">{{ __('dashboard.Please fix the following errors') }}:</h6>
                                            <ul class="mb-0 small">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <!-- Debug: Show old data if exists -->
                                    @if(old() && config('app.debug'))
                                        <div class="alert alert-info text-start mb-4">
                                            <h6 class="fw-bold mb-2">Debug - Old Input Data:</h6>
                                            <small>{{ json_encode(old()) }}</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeInUp"
                                    data-wow-delay="0.2s">

                                    <div class="ai-visit-places">
                                        <div class="days">
                                            <div class="day">
                                                @if (!@session('passengers'))
                                                    @for ($key = 0; $key < @session('preferences')['adults']; $key++)
                                                        <div class="collapse-div">
                                                            <p class="text-start">
                                                                <a class="btn btn-warning collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleAdults{{ $key }}"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExampleAdults{{ $key }}">
                                                                    {{ $key + 1 }}
                                                                </a>
                                                                <span style="width: 80%">
                                                                    <a  style="width: 100%;color:black;justify-content:start" class="collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleAdults{{ $key }}"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExampleAdults{{ $key }}">
                                                                    {{ __('dashboard.ADULT') }} {{ $key + 1 }}
                                                                </a>

                                                                </span>
                                                            </p>
                                                            <div class="collapse"
                                                                id="collapseExampleAdults{{ $key }}">
                                                                <div class="card mb-3 p-1">
                                                                    <div class="container">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group text-start">
                                                                                    <label class="fw-bold mb-3"
                                                                                        for="grid">{{ __('dashboard.Passenger details') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="exampleInputEmail1">{{ __('dashboard.First Name') }}</label>
                                                                                            <input type="text" required
                                                                                                class="form-control"
                                                                                                id="first_name"
                                                                                                name="first_name[]"
                                                                                                value="{{ getSavedValue('first_name', $key) }}"
                                                                                                placeholder="{{ __('dashboard.First Name') }}">
                                                                                            <input type="hidden"
                                                                                                name="pax_type[]"
                                                                                                value="1" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="last_name">{{ __('dashboard.Last Name') }}</label>
                                                                                            <input type="text" required
                                                                                                class="form-control"
                                                                                                id="last_name"
                                                                                                name="last_name[]"
                                                                                                value="{{ getSavedValue('last_name', $key) }}"
                                                                                                placeholder="{{ __('dashboard.Last Name') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="birth_date">{{ __('dashboard.Birth Date') }}</label>
                                                                                            <div class="">
                                                                                                <input type="date"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    id="birth_date"
                                                                                                    value="{{ getSavedValue('birth_date', $key) }}"
                                                                                                    name="birth_date[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="email">{{ __('dashboard.Email') }}</label>
                                                                                            <div class="">
                                                                                                <input type="email"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Email') }}"
                                                                                                    id="email"
                                                                                                    name="email[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="phone">{{ __('dashboard.Phone') }}</label>
                                                                                            <div class="">
                                                                                                <input type="number"
                                                                                                    required
                                                                                                    placeholder="{{ __('dashboard.Phone') }}"
                                                                                                    class="form-control"
                                                                                                    id="phone"
                                                                                                    name="phone[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>


                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group ">
                                                                                            <label
                                                                                                for="birth_date">{{ __('dashboard.Nationality') }}</label>
                                                                                            <select class="form-control"
                                                                                                name="nationality[]"
                                                                                                id="nationality" required>
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
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address">{{ __('dashboard.gender') }}</label>
                                                                                            <select class="form-control"
                                                                                                name="gender[]"
                                                                                                id="gender" required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    selected>
                                                                                                    {{ __('dashboard.Select Gender') }}
                                                                                                </option>
                                                                                                <option value="1">
                                                                                                    {{ __('dashboard.Male') }}
                                                                                                </option>
                                                                                                <option value="2">
                                                                                                    {{ __('dashboard.Female') }}
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address">{{ __('dashboard.Address') }}</label>
                                                                                            <div class="">
                                                                                                <input type="address"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Address') }}"
                                                                                                    id="address"
                                                                                                    name="address[]" />
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                                <div class="row mt-4">
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group text-start">
                                                                                            <label class="fw-bold mb-3"
                                                                                                for="grid">{{ __('dashboard.Document Type') }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_number">{{ __('dashboard.Passport Number') }}</label>
                                                                                            <input type="number" required
                                                                                                class="form-control"
                                                                                                id="passport_number"
                                                                                                name="passport_number[]"
                                                                                                aria-describedby="emailHelp"
                                                                                                placeholder="{{ __('dashboard.Passport Number') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_expiry_date">{{ __('dashboard.Passport Expiry Date') }}</label>
                                                                                            <div class="date-div">
                                                                                                <input type="text"
                                                                                                    required
                                                                                                    class="form-control date"
                                                                                                    id="passport_expiry_date"
                                                                                                    name="passport_expiry_date[]"
                                                                                                    placeholder="{{ __('dashboard.Passport Expiry Date') }}"
                                                                                                    value="">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @endif
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor

                                                    @for ($key = 0; $key < @session('preferences')['children']; $key++)
                                                        <div class="collapse-div">
                                                            <p class="text-start">
                                                                <a class="btn btn-warning collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleChildren{{ $key }}"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExampleChildren{{ $key }}">
                                                                    {{ $key + 1 }}
                                                                </a>
                                                                <span style="width: 80%">
                                                                    <a  style="width: 100%;color:black;justify-content:start" class="collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleChildren{{ $key }}"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExampleChildren{{ $key }}">
                                                                    {{ __('dashboard.Child') }} {{ $key + 1 }}
                                                                </a>

                                                                </span>
                                                            </p>
                                                            <div class="collapse"
                                                                id="collapseExampleChildren{{ $key }}">
                                                                <div class="card mb-3 p-1">
                                                                    <div class="container">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group text-start">
                                                                                    <label class="fw-bold mb-3"
                                                                                        for="grid">{{ __('dashboard.Passenger details') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="exampleInputEmail1">{{ __('dashboard.First Name') }}</label>
                                                                                            <input type="text" required
                                                                                                class="form-control"
                                                                                                id="first_name"
                                                                                                name="first_name[]"
                                                                                                placeholder="{{ __('dashboard.First Name') }}">
                                                                                            <input type="hidden"
                                                                                                name="pax_type[]"
                                                                                                value="2" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="last_name">{{ __('dashboard.Last Name') }}</label>
                                                                                            <input type="text" required
                                                                                                class="form-control"
                                                                                                id="last_name"
                                                                                                name="last_name[]"
                                                                                                placeholder="{{ __('dashboard.Last Name') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="birth_date">{{ __('dashboard.Birth Date') }}</label>
                                                                                            <div class="">
                                                                                                <input type="date"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    id="birth_date"
                                                                                                    name="birth_date[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="email">{{ __('dashboard.Email') }}</label>
                                                                                            <div class="">
                                                                                                <input type="email"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Email') }}"
                                                                                                    id="email"
                                                                                                    name="email[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="phone">{{ __('dashboard.Phone') }}</label>
                                                                                            <div class="">
                                                                                                <input type="number"
                                                                                                    required
                                                                                                    placeholder="{{ __('dashboard.Phone') }}"
                                                                                                    class="form-control"
                                                                                                    id="phone"
                                                                                                    name="phone[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>


                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                for="nationality">{{ __('dashboard.Nationality') }}</label>
                                                                                            <select class="form-control"
                                                                                                name="nationality[]"
                                                                                                id="nationality" required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    selected>
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
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address">{{ __('dashboard.gender') }}</label>
                                                                                            <select class="form-control"
                                                                                                name="gender[]"
                                                                                                id="gender" required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    selected>
                                                                                                    {{ __('dashboard.Select Gender') }}
                                                                                                </option>
                                                                                                <option value="1">
                                                                                                    {{ __('dashboard.Male') }}
                                                                                                </option>
                                                                                                <option value="2">
                                                                                                    {{ __('dashboard.Female') }}
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address">{{ __('dashboard.Address') }}</label>
                                                                                            <div class="">
                                                                                                <input type="address"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Address') }}"
                                                                                                    id="address"
                                                                                                    name="address[]" />
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                                <div class="row mt-4">
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group text-start">
                                                                                            <label class="fw-bold mb-3"
                                                                                                for="grid">{{ __('dashboard.Document Type') }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_number">{{ __('dashboard.Passport Number') }}</label>
                                                                                            <input type="number" required
                                                                                                class="form-control"
                                                                                                id="passport_number"
                                                                                                name="passport_number[]"
                                                                                                aria-describedby="emailHelp"
                                                                                                placeholder="{{ __('dashboard.Passport Number') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_expiry_date">{{ __('dashboard.Passport Expiry Date') }}</label>
                                                                                            <div class="date-div">
                                                                                                <input type="text"
                                                                                                    required
                                                                                                    class="form-control date"
                                                                                                    id="passport_expiry_date"
                                                                                                    name="passport_expiry_date[]"
                                                                                                    placeholder="{{ __('dashboard.Passport Expiry Date') }}"
                                                                                                    value="">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @endif
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor

                                                    @for ($key = 0; $key < @session('preferences')['infants']; $key++)
                                                        <div class="collapse-div">
                                                            <p class="text-start">
                                                                <a class="btn btn-warning collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleInfants{{ $key }}"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExampleInfants{{ $key }}">
                                                                    {{ $key + 1 }}
                                                                </a>
                                                                <span style="width: 80%">
                                                                    <a  style="width: 100%;color:black;justify-content:start" class="collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleInfants{{ $key }}"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExampleInfants{{ $key }}">
                                                                    {{ __('dashboard.Infant') }} {{ $key + 1 }}
                                                                </a>

                                                                </span>

                                                            </p>
                                                            <div class="collapse"
                                                                id="collapseExampleInfants{{ $key }}">
                                                                <div class="card mb-3 p-1">
                                                                    <div class="container">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group text-start">
                                                                                    <label class="fw-bold mb-3"
                                                                                        for="grid">{{ __('dashboard.Passenger details') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="exampleInputEmail1">{{ __('dashboard.First Name') }}</label>
                                                                                            <input type="text" required
                                                                                                class="form-control"
                                                                                                id="first_name"
                                                                                                name="first_name[]"
                                                                                                placeholder="{{ __('dashboard.First Name') }}">
                                                                                            <input type="hidden"
                                                                                                name="pax_type[]"
                                                                                                value="3" />
                                                                                            <!-- Infant-specific hidden fields for relationship -->
                                                                                            <input type="hidden" name="guardian_name[]" value="" />
                                                                                            <input type="hidden" name="infant_seat_type[]" value="lap" />
                                                                                            <input type="hidden" name="infant_special_requirements[]" value="" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="last_name">{{ __('dashboard.Last Name') }}</label>
                                                                                            <input type="text" required
                                                                                                class="form-control"
                                                                                                id="last_name"
                                                                                                name="last_name[]"
                                                                                                placeholder="{{ __('dashboard.Last Name') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="birth_date">{{ __('dashboard.Birth Date') }}</label>
                                                                                            <div class="">
                                                                                                <input type="date"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    id="birth_date"
                                                                                                    name="birth_date[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="email">{{ __('dashboard.Email') }}</label>
                                                                                            <div class="">
                                                                                                <input type="email"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Email') }}"
                                                                                                    id="email"
                                                                                                    name="email[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="phone">{{ __('dashboard.Phone') }}</label>
                                                                                            <div class="">
                                                                                                <input type="number"
                                                                                                    required
                                                                                                    placeholder="{{ __('dashboard.Phone') }}"
                                                                                                    class="form-control"
                                                                                                    id="phone"
                                                                                                    name="phone[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>


                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                for="birth_date">{{ __('dashboard.Nationality') }}</label>
                                                                                            <select class="form-control"
                                                                                                name="nationality[]"
                                                                                                id="nationality" required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    selected>
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
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address">{{ __('dashboard.gender') }}</label>
                                                                                            <select class="form-control"
                                                                                                name="gender[]"
                                                                                                id="gender" required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    selected>
                                                                                                    {{ __('dashboard.Select Gender') }}
                                                                                                </option>
                                                                                                <option value="1">
                                                                                                    {{ __('dashboard.Male') }}
                                                                                                </option>
                                                                                                <option value="2">
                                                                                                    {{ __('dashboard.Female') }}
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address">{{ __('dashboard.Address') }}</label>
                                                                                            <div class="">
                                                                                                <input type="address"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Address') }}"
                                                                                                    id="address"
                                                                                                    name="address[]" />
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                                <div class="row mt-4">
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group text-start">
                                                                                            <label class="fw-bold mb-3"
                                                                                                for="grid">{{ __('dashboard.Document Type') }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_number">{{ __('dashboard.Passport Number') }}</label>
                                                                                            <input type="number" required
                                                                                                class="form-control"
                                                                                                id="passport_number"
                                                                                                name="passport_number[]"
                                                                                                aria-describedby="emailHelp"
                                                                                                placeholder="{{ __('dashboard.Passport Number') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_expiry_date">{{ __('dashboard.Passport Expiry Date') }}</label>
                                                                                            <div class="date-div">
                                                                                                <input type="text"
                                                                                                    required
                                                                                                    class="form-control date"
                                                                                                    id="passport_expiry_date"
                                                                                                    name="passport_expiry_date[]"
                                                                                                    placeholder="{{ __('dashboard.Passport Expiry Date') }}"
                                                                                                    value="">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @endif
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                @else
                                                    @foreach (@session('passengers') as $key => $passenger)
                                                        <div class="collapse-div">
                                                            <p class="text-start">
                                                                <a class="btn btn-warning collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleAdults{{ $key }}"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExampleAdults{{ $key }}">
                                                                    {{ $key + 1 }}
                                                                </a>
                                                                <span style="width: 80%">
                                                                    <a  style="width: 100%;color:black;justify-content:start" class="collapsed"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleAdults{{ $key }}"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExampleAdults{{ $key }}">
                                                                    @if ($passenger['pax_type'] == 1)
                                                                        {{ __('dashboard.ADULT') }}
                                                                    @endif
                                                                    @if ($passenger['pax_type'] == 2)
                                                                        {{ __('dashboard.Child') }}
                                                                    @endif
                                                                    @if ($passenger['pax_type'] == 3)
                                                                        {{ __('dashboard.Infant') }}
                                                                    @endif
                                                                    {{ $key + 1 }}
                                                                </a>

                                                                </span>

                                                            </p>
                                                            <div class="collapse"
                                                                id="collapseExampleAdults{{ $key }}">
                                                                <div class="card mb-3 p-1">
                                                                    <div class="container">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group text-start">
                                                                                    <label class="fw-bold mb-3"
                                                                                        for="grid">{{ __('dashboard.Passenger details') }}</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="exampleInputEmail1">{{ __('dashboard.First Name') }}</label>
                                                                                            <input type="text" required
                                                                                                class="form-control"
                                                                                                id="first_name"
                                                                                                name="first_name[]"
                                                                                                value="{{ $passenger['first_name'] }}"
                                                                                                placeholder="{{ __('dashboard.First Name') }}">

                                                                                            <input type="hidden"
                                                                                                name="pax_type[]"
                                                                                                value="{{ $passenger['pax_type'] }}" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="last_name">{{ __('dashboard.Last Name') }}</label>
                                                                                            <input type="text" required
                                                                                                class="form-control"
                                                                                                id="last_name"
                                                                                                value="{{ $passenger['last_name'] }}"
                                                                                                name="last_name[]"
                                                                                                placeholder="{{ __('dashboard.Last Name') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="birth_date">{{ __('dashboard.Birth Date') }}</label>
                                                                                            <div class="">
                                                                                                <input type="date"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    id="birth_date"
                                                                                                    value="{{ $passenger['birth_date'] }}"
                                                                                                    name="birth_date[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="email">{{ __('dashboard.Email') }}</label>
                                                                                            <div class="">
                                                                                                <input type="email"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Email') }}"
                                                                                                    id="email"
                                                                                                    value="{{ $passenger['email'] }}"
                                                                                                    name="email[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="phone">{{ __('dashboard.Phone') }}</label>
                                                                                            <div class="">
                                                                                                <input type="number"
                                                                                                    required
                                                                                                    placeholder="{{ __('dashboard.Phone') }}"
                                                                                                    class="form-control"
                                                                                                    id="phone"
                                                                                                    value="{{ $passenger['phone'] }}"
                                                                                                    name="phone[]">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>


                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group row">
                                                                                            <label
                                                                                                for="birth_date">{{ __('dashboard.Nationality') }}</label>
                                                                                            <select class="form-control"
                                                                                                name="nationality[]"
                                                                                                id="nationality" required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    selected>
                                                                                                    {{ __('dashboard.Select Nationality') }}
                                                                                                </option>
                                                                                                @foreach (\App\Models\Country::all() as $country)
                                                                                                    <option
                                                                                                        value="{{ $country->code }}"
                                                                                                        {{ $passenger['nationality']== $country->code?'selected':'' }}>
                                                                                                        {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address">{{ __('dashboard.gender') }}</label>
                                                                                            <select class="form-control"
                                                                                                name="gender[]"
                                                                                                id="gender" required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    selected>
                                                                                                    {{ __('dashboard.Select Gender') }}
                                                                                                </option>
                                                                                                <option value="1" {{$passenger['gender'] == "1" ?'selected':''}}>
                                                                                                    {{ __('dashboard.Male') }}
                                                                                                </option>
                                                                                                <option value="2" {{$passenger['gender'] == "2" ?'selected':''}}>
                                                                                                    {{ __('dashboard.Female') }}
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address">{{ __('dashboard.Address') }}</label>
                                                                                            <div class="">
                                                                                                <input type="address"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    value="{{$passenger['address']}}"
                                                                                                    placeholder="{{ __('dashboard.Address') }}"
                                                                                                    id="address"
                                                                                                    name="address[]" />
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                                <div class="row mt-4">
                                                                                    <div class="col-md-12">
                                                                                        <div class="form-group text-start">
                                                                                            <label class="fw-bold mb-3"
                                                                                                for="grid">{{ __('dashboard.Document Type') }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_number">{{ __('dashboard.Passport Number') }}</label>
                                                                                            <input type="number" required
                                                                                                class="form-control"
                                                                                           value="{{$passenger['passport_number']}}"
                                                                                                min="1"
                                                                                                max="999999999999999"
                                                                                                id="passport_number"
                                                                                                name="passport_number[]"
                                                                                                aria-describedby="emailHelp"
                                                                                                placeholder="{{ __('dashboard.Passport Number') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_expiry_date">{{ __('dashboard.Passport Expiry Date') }}</label>
                                                                                            <div class="">
                                                                                                <input type="date"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    id="passport_expiry_date"
                                                                                                    value="{{$passenger['passport_expiry_date']}}"
                                                                                                    name="passport_expiry_date[]"
                                                                                                    placeholder="{{ __('dashboard.Passport Expiry Date') }}"
                                                                                                    >
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @endif
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                    <button type="submit"
                                        class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn w-50">
                                        {{ __('dashboard.Save') }}
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Steps form End -->

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug information
        @if(old())
            console.log('Old input data found:', @json(old()));
        @else
            console.log('No old input data found');
        @endif
        
        @if($errors->any())
            console.log('Validation errors found:', @json($errors->all()));
        @else
            console.log('No validation errors');
        @endif
        
        // Restore form values from Laravel's old input (with slight delay to ensure DOM is ready)
        setTimeout(restoreFormValues, 100);
        
        // Guest name validation rules
        const namePattern = /^[a-zA-Z\s\-'\.]+$/; // Only letters, spaces, hyphens, apostrophes, and dots
        const numberPattern = /\d/; // Numbers detection
        
        // Function to restore form values from Laravel's old input
        function restoreFormValues() {
            @if(old())
                const oldData = @json(old());
                
                // Restore array-based inputs (name[], email[], etc.)
                Object.keys(oldData).forEach(fieldName => {
                    if (Array.isArray(oldData[fieldName])) {
                        const inputs = document.querySelectorAll(`input[name="${fieldName}[]"], select[name="${fieldName}[]"]`);
                        oldData[fieldName].forEach((value, index) => {
                            if (inputs[index] && value !== null && value !== undefined) {
                                if (inputs[index].type === 'checkbox' || inputs[index].type === 'radio') {
                                    inputs[index].checked = value == inputs[index].value;
                                } else {
                                    inputs[index].value = value;
                                }
                            }
                        });
                    } else if (oldData[fieldName] !== null && oldData[fieldName] !== undefined) {
                        // Handle non-array fields
                        const input = document.querySelector(`input[name="${fieldName}"], select[name="${fieldName}"]`);
                        if (input) {
                            if (input.type === 'checkbox' || input.type === 'radio') {
                                input.checked = oldData[fieldName] == input.value;
                            } else {
                                input.value = oldData[fieldName];
                            }
                        }
                    }
                });
                
                console.log('Form values restored from old input:', oldData);
            @endif
        }
        
        // Function to validate name input
        function validateNameInput(input, fieldType) {
            const value = input.value.trim();
            const errorContainer = getOrCreateErrorContainer(input);
            
            // Clear previous errors
            errorContainer.textContent = '';
            input.classList.remove('is-invalid');
            
            // Check minimum length
            if (value.length < 2 && value.length > 0) {
                showError(input, errorContainer, `${fieldType} must be at least 2 characters long.`);
                return false;
            }
            
            // Check for numbers
            if (numberPattern.test(value)) {
                showError(input, errorContainer, `${fieldType} cannot contain numbers.`);
                return false;
            }
            
            // Check for invalid characters
            if (!namePattern.test(value) && value.length > 0) {
                showError(input, errorContainer, `${fieldType} can only contain letters, spaces, hyphens, apostrophes, and dots.`);
                return false;
            }
            
            // For first names, check for duplicates
            if (fieldType === 'First name') {
                if (checkDuplicateFirstNames(input)) {
                    showError(input, errorContainer, 'Duplicate first names are not allowed. Each guest must have a unique first name.');
                    return false;
                }
            }
            
            return true;
        }
        
        // Function to check for duplicate first names
        function checkDuplicateFirstNames(currentInput) {
            const currentValue = currentInput.value.trim().toLowerCase();
            if (currentValue === '') return false;
            
            const allFirstNameInputs = document.querySelectorAll('input[name="first_name[]"]');
            let duplicateCount = 0;
            
            allFirstNameInputs.forEach(input => {
                if (input.value.trim().toLowerCase() === currentValue) {
                    duplicateCount++;
                }
            });
            
            return duplicateCount > 1;
        }
        
        // Function to get or create error container
        function getOrCreateErrorContainer(input) {
            let errorContainer = input.parentNode.querySelector('.name-validation-error');
            if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.className = 'name-validation-error text-danger small mt-1';
                input.parentNode.appendChild(errorContainer);
            }
            return errorContainer;
        }
        
        // Function to show error
        function showError(input, errorContainer, message) {
            input.classList.add('is-invalid');
            errorContainer.textContent = message;
        }
        
        // Function to clear all duplicate first name errors
        function clearDuplicateErrors() {
            const allFirstNameInputs = document.querySelectorAll('input[name="first_name[]"]');
            allFirstNameInputs.forEach(input => {
                const errorContainer = input.parentNode.querySelector('.name-validation-error');
                if (errorContainer && errorContainer.textContent.includes('Duplicate first names')) {
                    errorContainer.textContent = '';
                    input.classList.remove('is-invalid');
                }
            });
        }
        
        // Add event listeners to all first name inputs
        const firstNameInputs = document.querySelectorAll('input[name="first_name[]"]');
        firstNameInputs.forEach(input => {
            // Real-time validation on input
            input.addEventListener('input', function() {
                // Clear all duplicate errors first
                clearDuplicateErrors();
                
                // Validate current input
                validateNameInput(this, 'First name');
                
                // Check duplicates for all first name inputs
                firstNameInputs.forEach(otherInput => {
                    if (otherInput.value.trim() !== '') {
                        validateNameInput(otherInput, 'First name');
                    }
                });
            });
            
            // Validation on blur
            input.addEventListener('blur', function() {
                validateNameInput(this, 'First name');
            });
        });
        
        // Add event listeners to all last name inputs
        const lastNameInputs = document.querySelectorAll('input[name="last_name[]"]');
        lastNameInputs.forEach(input => {
            input.addEventListener('input', function() {
                validateNameInput(this, 'Last name');
            });
            
            input.addEventListener('blur', function() {
                validateNameInput(this, 'Last name');
            });
        });
        
        // Function to validate child age
        function validateChildAge(birthDateInput, paxTypeInput) {
            const birthDate = new Date(birthDateInput.value);
            const today = new Date();
            const age = Math.floor((today - birthDate) / (365.25 * 24 * 60 * 60 * 1000));
            const paxType = parseInt(paxTypeInput.value);
            
            // Clear previous errors
            clearValidationError(birthDateInput);
            
            if (paxType === 2) { // Child
                if (age < 2 || age >= 12) {
                    showValidationError(birthDateInput, '{{ __("dashboard.child_age_validation") }}');
                    return false;
                }
            }
            return true;
        }
        
        // Function to validate passport expiry date
        function validatePassportExpiry(passportExpiryInput) {
            if (!passportExpiryInput.value) return true; // Skip if empty (required validation handles this)
            
            const expiryDate = new Date(passportExpiryInput.value);
            const sixMonthsFromNow = new Date();
            sixMonthsFromNow.setMonth(sixMonthsFromNow.getMonth() + 6);
            
            // Clear previous errors
            clearValidationError(passportExpiryInput);
            
            if (expiryDate <= sixMonthsFromNow) {
                showValidationError(passportExpiryInput, '{{ __("dashboard.passport_expiry_min_months") }}');
                return false;
            }
            return true;
        }
        
        // Function to show validation error
        function showValidationError(input, message) {
            input.classList.add('is-invalid');
            let errorContainer = input.parentNode.querySelector('.validation-error');
            if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.className = 'validation-error text-danger small mt-1';
                input.parentNode.appendChild(errorContainer);
            }
            errorContainer.textContent = message;
        }
        
        // Function to clear validation error
        function clearValidationError(input) {
            input.classList.remove('is-invalid');
            const errorContainer = input.parentNode.querySelector('.validation-error');
            if (errorContainer) {
                errorContainer.textContent = '';
            }
        }
        
        // Add event listeners for birth date validation
        const birthDateInputs = document.querySelectorAll('input[name="birth_date[]"]');
        const paxTypeInputs = document.querySelectorAll('input[name="pax_type[]"]');
        
        // Set date limits for child birth dates
        const today = new Date();
        const minChildDate = new Date(today.getFullYear() - 11, today.getMonth(), today.getDate());
        const maxChildDate = new Date(today.getFullYear() - 2, today.getMonth(), today.getDate());
        
        birthDateInputs.forEach((input, index) => {
            const paxType = parseInt(paxTypeInputs[index].value);
            
            // Set date constraints for children
            if (paxType === 2) {
                input.setAttribute('min', minChildDate.toISOString().split('T')[0]);
                input.setAttribute('max', maxChildDate.toISOString().split('T')[0]);
            }
            
            input.addEventListener('change', function() {
                validateChildAge(this, paxTypeInputs[index]);
            });
        });
        
        // Add event listeners for passport expiry validation
        const passportExpiryInputs = document.querySelectorAll('input[name="passport_expiry_date[]"]');
        const minPassportDate = new Date();
        minPassportDate.setMonth(minPassportDate.getMonth() + 6);
        
        passportExpiryInputs.forEach(input => {
            // Set minimum date to 6 months from now
            input.setAttribute('min', minPassportDate.toISOString().split('T')[0]);
            
            input.addEventListener('change', function() {
                validatePassportExpiry(this);
            });
        });

        // Form submission validation
        const form = document.querySelector('.steps-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validate all first names
                firstNameInputs.forEach(input => {
                    if (!validateNameInput(input, 'First name')) {
                        isValid = false;
                    }
                });
                
                // Validate all last names
                lastNameInputs.forEach(input => {
                    if (!validateNameInput(input, 'Last name')) {
                        isValid = false;
                    }
                });
                
                // Validate child ages
                birthDateInputs.forEach((input, index) => {
                    if (!validateChildAge(input, paxTypeInputs[index])) {
                        isValid = false;
                    }
                });
                
                // Validate passport expiry dates
                passportExpiryInputs.forEach(input => {
                    if (!validatePassportExpiry(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Show general alert
                    alert('Please fix all validation errors before submitting the form.');
                    
                    // Focus on first invalid input
                    const firstInvalidInput = document.querySelector('.is-invalid');
                    if (firstInvalidInput) {
                        firstInvalidInput.focus();
                        firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }
        
        // Add styling for invalid inputs
        const style = document.createElement('style');
        style.textContent = `
            .is-invalid {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }
            .name-validation-error {
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }
        `;
        document.head.appendChild(style);
    });
    </script>
@endsection
