<div style="z-index: 1050;" class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div style="width: 100%;margin:auto" class="modal-content">
            <div class="modal-header d-flex justify-content-between">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <div class="text-center mx-auto" style="max-width: 100%;">
                        <h4 class="mb-1 section-title mt-1 text-start">{{ __('dashboard.passenger_information') }}</h4>
                        <p>{{ __('dashboard.Passenger information must match government-issued') }}</p>
                    </div>
                </h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/passenger-information" class="steps-form" method="POST" id="passengerForm">
                @csrf
                <!-- Validation Error Container -->
                <div id="validation-errors" class="alert alert-danger" style="display: none; margin: 15px;">
                    <ul id="error-list" class="mb-0"></ul>
                </div>
                <!-- Success Message Container -->
                <div id="success-message" class="alert alert-success" style="display: none; margin: 15px;">
                    <span id="success-text"></span>
                </div>
                <div class="modal-body">
                    <!-- Data Persistence Indicator -->
                    <div id="data-persistence-indicator" class="alert alert-info" style="display: none; margin-bottom: 15px;">
                        <i class="fas fa-save"></i> <span id="persistence-text">Your form data is automatically saved as you type.</span>
                        <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="restoreFormData(); console.log('Manual restore triggered');">
                            <i class="fas fa-redo"></i> Restore Data
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="console.log('Stored data:', localStorage.getItem('passengerFormData')); console.log('Session data:', sessionStorage.getItem('passengerFormData'));">
                            <i class="fas fa-info"></i> Debug
                        </button>
                    </div>
                    
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="form-inputs bg-grey text-center mx-auto mb-5" data-wow-delay="0.2s">

                                    <div class="ai-visit-places">
                                        <div class="days">
                                            <div class="day">
                                                @if (!@session('passengers'))
                                                    @for ($key = 0; $key < @session('preferences')['adults']; $key++)
                                                        <div class="collapse-div">
                                                            <p class="text-start">
                                                                <a class="btn btn-warning {{$key==0?'':'collapsed'}}"
                                                                    data-bs-toggle="collapse"
                                                                    href="#collapseExampleAdults{{ $key }}"
                                                                    role="button" aria-expanded="{{$key==0?'true':'false'}}"
                                                                    aria-controls="collapseExampleAdults{{ $key }}">
                                                                    {{ $key + 1 }}
                                                                </a>
                                                                <span style="width: 80%">
                                                                    <a style="width: 100%;color:black;justify-content:start"
                                                                        class="collapsed" data-bs-toggle="collapse"
                                                                        href="#collapseExampleAdults{{ $key }}"
                                                                        role="button" aria-expanded="false"
                                                                        aria-controls="collapseExampleAdults{{ $key }}">
                                                                        {{ __('dashboard.ADULT') }} {{ $key + 1 }}
                                                                    </a>

                                                                </span>
                                                            </p>
                                                            <div class="collapse {{$key==0?'show':''}}"
                                                                id="collapseExampleAdults{{ $key }}">
                                                                <div class="card mb-3 p-1">
                                                                    <div class="container">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="form-group text-start">
                                                                                    <label class="fw-bold mb-3"
                                                                                        for="grid">
                                                                                        {{ __('dashboard.Passenger details') }}
                                                                                        {{$key==0? (__('dashboard.ADULT').' 1'):''}}

                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="title{{$key}}_adults">{{ __('dashboard.surname') }}</label>
                                                                                            <select
                                                                                                class="form-control title-select"
                                                                                                name="title[]"
                                                                                                id="title{{$key}}_adults"
                                                                                                required>
                                                                                                <option  
                                                                                                        value="0"
                                                                                                        {{ old('title.' . $key, '0') == '0' ? 'selected' : '' }}>
                                                                                                    Mr
                                                                                                </option>
                                                                                                <option value="1"
                                                                                                        {{ old('title.' . $key) == '1' ? 'selected' : '' }}>
                                                                                                    Miss
                                                                                                </option>
                                                                                                <option value="2"
                                                                                                        {{ old('title.' . $key) == '2' ? 'selected' : '' }}>
                                                                                                    Mrs
                                                                                                </option>

                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="first_name{{$key}}_adults">{{ __('dashboard.First Name') }}</label>
                                                                                            <input type="text"
                                                                                                required
                                                                                                class="form-control"
                                                                                                id="first_name{{$key}}_adults"
                                                                                                name="first_name[]"
                                                                                                value="{{ old('first_name.' . $key) }}"
                                                                                                placeholder="{{ __('dashboard.First Name') }}">
                                                                                            <input type="hidden"
                                                                                                name="pax_type[]"
                                                                                                value="1" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="last_name{{$key}}_adults">{{ __('dashboard.Last Name') }}</label>
                                                                                            <input type="text"
                                                                                                required
                                                                                                class="form-control"
                                                                                                id="last_name{{$key}}_adults"
                                                                                                name="last_name[]"
                                                                                                value="{{ old('last_name.' . $key) }}"
                                                                                                placeholder="{{ __('dashboard.Last Name') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="birth_date{{$key}}_adults">{{ __('dashboard.Birth Date') }}</label>
                                                                                            <div class="">
                                                                                                <input type="date"
                                                                                                    required
                                                                                                    class="form-control adult-birth-date"
                                                                                                    id="birth_date{{$key}}_adults"
                                                                                                    name="birth_date[]"
                                                                                                    value="{{ old('birth_date.' . $key) }}"
                                                                                                    min="{{ date('Y-m-d', strtotime('-160 years')) }}"
                                                                                                    max="{{ date('Y-m-d', strtotime('-19 years')) }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="email{{$key}}_adults">{{ __('dashboard.Email') }}</label>
                                                                                            <div class="">
                                                                                                <input type="email"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Email') }}"
                                                                                                    id="email email{{$key}}_adults"
                                                                                                    name="email[]"
                                                                                                    value="{{ old('email.' . $key) }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="phone{{$key}}_adults">{{ __('dashboard.Phone') }}</label>
                                                                                            <div class="">
                                                                                                <input type="number"
                                                                                                    required
                                                                                                    placeholder="{{ __('dashboard.Phone') }}"
                                                                                                    class="form-control"
                                                                                                    id="phone phone{{$key}}_adults"
                                                                                                    name="phone[]"
                                                                                                    value="{{ old('phone.' . $key) }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                   
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="nationality{{$key}}_adults">{{ __('dashboard.Nationality') }}</label>
                                                                                            <select
                                                                                                class="form-control"
                                                                                                name="nationality[]"
                                                                                                id="nationality nationality{{$key}}_adults"
                                                                                                required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    {{ !session('guest_nationality') && !old('nationality.' . $key) ? 'selected' : '' }}>
                                                                                                    {{ __('dashboard.Select Nationality') }}
                                                                                                </option>
                                                                                                @foreach (\App\Models\Country::all() as $country)
                                                                                                    <option
                                                                                                        value="{{ $country->code }}"
                                                                                                        {{ (old('nationality.' . $key) == $country->code) || (session('guest_nationality') == $country->code && !old('nationality.' . $key)) ? 'selected' : '' }}>
                                                                                                        {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address{{$key}}_adults">{{ __('dashboard.Address') }}</label>
                                                                                            <div class="">
                                                                                                <input type="address"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Address') }}"
                                                                                                    id="address address{{$key}}_adults"
                                                                                                    name="address[]"
                                                                                                    value="{{ old('address.' . $key) }}" />
                                                                                                <!-- Hidden gender field that will be auto-populated based on title -->
                                                                                                <input type="hidden" name="gender[]" class="auto-gender" value="1">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                               
                                                                                @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                                <div class="row mt-4">
                                                                                   
                                                                                    <div class="col-md-12">
                                                                                        <div
                                                                                            class="form-group text-start">
                                                                                            <label class="fw-bold mb-3"
                                                                                                for="grid">{{ __('dashboard.Document Type') }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_number{{$key}}_adults">{{ __('dashboard.Passport Number') }}</label>
                                                                                            <input type="number"
                                                                                                required
                                                                                                class="form-control"
                                                                                                id="passport_number passport_number{{$key}}_adults"
                                                                                                name="passport_number[]"
                                                                                                minlength="6"
                                                                                                maxlength="15"
                                                                                                aria-describedby="emailHelp"
                                                                                                placeholder="{{ __('dashboard.Passport Number') }}"
                                                                                                value="{{ old('passport_number.' . $key) }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_expiry_date{{$key}}_adults">{{ __('dashboard.Passport Expiry Date') }}</label>
                                                                                            <div class="date-div">
                                                                                                <input type="date"
                                                                                                    required
                                                                                                    class="form-control date"
                                                                                                    id="passport_expiry_date passport_expiry_date{{$key}}_adults"
                                                                                                    name="passport_expiry_date[]"
                                                                                                    placeholder="{{ __('dashboard.Passport Expiry Date') }}"
                                                                                                    min="{{ date('Y-m-d', strtotime('+6 months')) }}"
                                                                                                    value="{{ old('passport_expiry_date.' . $key) }}">
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
                                                                    <a style="width: 100%;color:black;justify-content:start"
                                                                        class="collapsed" data-bs-toggle="collapse"
                                                                        href="#collapseExampleChildren{{ $key }}"
                                                                        role="button" aria-expanded="false"
                                                                        aria-controls="collapseExampleChildren{{ $key }}">
                                                                        {{ __('dashboard.Child') }}
                                                                        {{ $key + 1 }}
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
                                                                                                for="first_name{{$key}}_children">{{ __('dashboard.First Name') }}</label>
                                                                                            <input type="text"
                                                                                                required
                                                                                                class="form-control"
                                                                                                id="first_name first_name{{$key}}_children"
                                                                                                name="first_name[]"
                                                                                                value="{{ old('first_name.' . (session('preferences')['adults'] + $key)) }}"
                                                                                                placeholder="{{ __('dashboard.First Name') }}">
                                                                                            <input type="hidden"
                                                                                                name="pax_type[]"
                                                                                                value="2" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="last_name{{$key}}_children">{{ __('dashboard.Last Name') }}</label>
                                                                                            <input type="text"
                                                                                                required
                                                                                                class="form-control"
                                                                                                id="last_name last_name{{$key}}_children"
                                                                                                name="last_name[]"
                                                                                                value="{{ old('last_name.' . (session('preferences')['adults'] + $key)) }}"
                                                                                                placeholder="{{ __('dashboard.Last Name') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="birth_date{{$key}}_children">{{ __('dashboard.Birth Date') }}</label>
                                                                                            <div class="">
                                                                                                <input type="date"
                                                                                                    required
                                                                                                    class="form-control child-birth-date"
                                                                                                    id="birth_date birth_date{{$key}}_children"
                                                                                                    name="birth_date[]"
                                                                                                    value="{{ old('birth_date.' . (session('preferences')['adults'] + $key)) }}"
                                                                                                    min="{{ date('Y-m-d', strtotime('-12 years')) }}"
                                                                                                    max="{{ date('Y-m-d', strtotime('-2 year')) }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="email{{$key}}_children">{{ __('dashboard.Email') }}</label>
                                                                                            <div class="">
                                                                                                <input type="email"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Email') }}"
                                                                                                    id="email email{{$key}}_children"
                                                                                                    name="email[]"
                                                                                                    value="{{ old('email.' . (session('preferences')['adults'] + $key)) }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="phone{{$key}}_children">{{ __('dashboard.Phone') }}</label>
                                                                                            <div class="">
                                                                                                <input type="number"
                                                                                                    required
                                                                                                    placeholder="{{ __('dashboard.Phone') }}"
                                                                                                    class="form-control"
                                                                                                    id="phone phone{{$key}}_children"
                                                                                                    name="phone[]"
                                                                                                    value="{{ old('phone.' . (session('preferences')['adults'] + $key)) }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>


                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="nationality{{$key}}_children">{{ __('dashboard.Nationality') }}</label>
                                                                                            <select
                                                                                                class="form-control"
                                                                                                name="nationality[]"
                                                                                                id="nationality nationality{{$key}}_children"
                                                                                                required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    {{ !old('nationality.' . (session('preferences')['adults'] + $key), session('guest_nationality')) ? 'selected' : '' }}>
                                                                                                    {{ __('dashboard.Select Nationality') }}
                                                                                                </option>
                                                                                                @foreach (\App\Models\Country::all() as $country)
                                                                                                    <option
                                                                                                        value="{{ $country->code }}"
                                                                                                        {{ old('nationality.' . (session('preferences')['adults'] + $key), session('guest_nationality')) == $country->code ? 'selected' : '' }}>
                                                                                                        {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="gender{{$key}}_children">{{ __('dashboard.gender') }}</label>
                                                                                            <select
                                                                                                class="form-control" style="z-index: 99999"
                                                                                                name="gender[]"
                                                                                                id="gender gender{{$key}}_children"
                                                                                                required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    {{ !old('gender.' . (session('preferences')['adults'] + $key)) ? 'selected' : '' }}>
                                                                                                    {{ __('dashboard.Select Gender') }}
                                                                                                </option>
                                                                                                <option value="1" {{ old('gender.' . (session('preferences')['adults'] + $key)) == '1' ? 'selected' : '' }}>
                                                                                                    {{ __('dashboard.Male') }}
                                                                                                </option>
                                                                                                <option value="2" {{ old('gender.' . (session('preferences')['adults'] + $key)) == '2' ? 'selected' : '' }}>
                                                                                                    {{ __('dashboard.Female') }}
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="address{{$key}}_children">{{ __('dashboard.Address') }}</label>
                                                                                            <div class="">
                                                                                                <input type="address"
                                                                                                    required
                                                                                                    class="form-control"
                                                                                                    placeholder="{{ __('dashboard.Address') }}"
                                                                                                    id="address address{{$key}}_children"
                                                                                                    name="address[]"
                                                                                                    value="{{ old('address.' . (session('preferences')['adults'] + $key)) }}" />
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mt-4">
                                                                                    
                                                                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                                   <div class="col-md-12">
                                                                                        <div
                                                                                            class="form-group text-start">
                                                                                            <label class="fw-bold mb-3"
                                                                                                for="grid">{{ __('dashboard.Document Type') }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_number{{$key}}_children">{{ __('dashboard.Passport Number') }}</label>
                                                                                            <input type="number"
                                                                                                required
                                                                                                class="form-control"
                                                                                                id="passport_number passport_number{{$key}}_children"
                                                                                                name="passport_number[]"
                                                                                                value="{{ old('passport_number.' . (session('preferences')['adults'] + $key)) }}"
                                                                                                minlength="6"
                                                                                                maxlength="15"
                                                                                                aria-describedby="emailHelp"
                                                                                                placeholder="{{ __('dashboard.Passport Number') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_expiry_date{{$key}}_children">{{ __('dashboard.Passport Expiry Date') }}</label>
                                                                                            <div class="date-div">
                                                                                                <input type="text"
                                                                                                    required
                                                                                                    class="form-control date"
                                                                                                    id="passport_expiry_date passport_expiry_date{{$key}}_children"
                                                                                                    name="passport_expiry_date[]"
                                                                                                    placeholder="{{ __('dashboard.Passport Expiry Date') }}"
                                                                                                    min="{{ date('Y-m-d', strtotime('+6 months')) }}"
                                                                                                    value="{{ old('passport_expiry_date.' . (session('preferences')['adults'] + $key)) }}">
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
                                                        </div>
                                                    @endfor

                                                     @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))

                                                    
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
                                                                    <a style="width: 100%;color:black;justify-content:start"
                                                                        class="collapsed" data-bs-toggle="collapse"
                                                                        href="#collapseExampleInfants{{ $key }}"
                                                                        role="button" aria-expanded="false"
                                                                        aria-controls="collapseExampleInfants{{ $key }}">
                                                                        {{ __('dashboard.Infant') }}
                                                                        {{ $key + 1 }}
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
                                                                                            <input type="text"
                                                                                                required
                                                                                                class="form-control"
                                                                                                id="first_name"
                                                                                                name="first_name[]"
                                                                                                placeholder="{{ __('dashboard.First Name') }}">
                                                                                            <input type="hidden"
                                                                                                name="pax_type[]"
                                                                                                value="3" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="last_name">{{ __('dashboard.Last Name') }}</label>
                                                                                            <input type="text"
                                                                                                required
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
                                                                                                    class="form-control infant-birth-date"
                                                                                                    id="birth_date"
                                                                                                    name="birth_date[]"
                                                                                                    min="{{ date('Y-m-d', strtotime('-1 years')) }}"
                                                                                                    max="{{ date('Y-m-d') }}">
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
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="nationality">{{ __('dashboard.Nationality') }}</label>
                                                                                            <select
                                                                                                class="form-control"
                                                                                                name="nationality[]"
                                                                                                id="nationality"
                                                                                                required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    {{ !session('guest_nationality') ? 'selected' : '' }}>
                                                                                                    {{ __('dashboard.Select Nationality') }}
                                                                                                </option>
                                                                                                @foreach (\App\Models\Country::all() as $country)
                                                                                                    <option
                                                                                                        value="{{ $country->code }}"
                                                                                                        {{ session('guest_nationality') == $country->code ? 'selected' : '' }}>
                                                                                                        {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="gender">{{ __('dashboard.gender') }}</label>
                                                                                            <select
                                                                                                class="form-control"
                                                                                                name="gender[]"
                                                                                                id="gender"
                                                                                                required>
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

                                                                                <div class="row mt-4">
                                                                                    <div class="col-md-12">
                                                                                        <div
                                                                                            class="form-group text-start">
                                                                                            <label class="fw-bold mb-3"
                                                                                                for="grid">{{ __('dashboard.Document Type') }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_number">{{ __('dashboard.Passport Number') }}</label>
                                                                                            <input type="number"
                                                                                                required
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
                                                                                                    min="{{ date('Y-m-d', strtotime('+6 months')) }}"
                                                                                                    value="">
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
                                                    @endfor
                                                     @endif
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
                                                                    <a style="width: 100%;color:black;justify-content:start"
                                                                        class="collapsed" data-bs-toggle="collapse"
                                                                        href="#collapseExampleAdults{{ $key }}"
                                                                        role="button" aria-expanded="false"
                                                                        aria-controls="collapseExampleAdults{{ $key }}">
                                                                        @if ($passenger['pax_type'] == 1)
                                                                            {{ __('dashboard.ADULT') }}
                                                                        @endif
                                                                        @if ($passenger['pax_type'] == 2)
                                                                            {{ __('dashboard.Child') }}
                                                                        @endif
                                                                        {{-- Commented out infant display
                                                                        @if ($passenger['pax_type'] == 3)
                                                                            {{ __('dashboard.Infant') }}
                                                                        @endif
                                                                        --}}
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
                                                                                            <input type="text"
                                                                                                required
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
                                                                                            <input type="text"
                                                                                                required
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
                                                                                                    class="form-control @if($passenger['pax_type'] == 1) adult-birth-date @elseif($passenger['pax_type'] == 2) child-birth-date @else infant-birth-date @endif"
                                                                                                    id="birth_date"
                                                                                                    value="{{ $passenger['birth_date'] }}"
                                                                                                    name="birth_date[]"
                                                                                                    @if($passenger['pax_type'] == 1)
                                                                                                        min="{{ date('Y-m-d', strtotime('-160 years')) }}"
                                                                                                        max="{{ date('Y-m-d', strtotime('-19 years')) }}"
                                                                                                    @elseif($passenger['pax_type'] == 2)
                                                                                                        min="{{ date('Y-m-d', strtotime('-18 years')) }}"
                                                                                                        max="{{ date('Y-m-d', strtotime('-1 year')) }}"
                                                                                                    @else
                                                                                                        min="{{ date('Y-m-d', strtotime('-2 years')) }}"
                                                                                                        max="{{ date('Y-m-d') }}"
                                                                                                    @endif
                                                                                                    >
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
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="nationality">{{ __('dashboard.Nationality') }}</label>
                                                                                            <select
                                                                                                class="form-control"
                                                                                                name="nationality[]"
                                                                                                id="nationality"
                                                                                                required>
                                                                                                <option disabled hidden
                                                                                                    value=""
                                                                                                    selected>
                                                                                                    {{ __('dashboard.Select Nationality') }}
                                                                                                </option>
                                                                                                @foreach (\App\Models\Country::all() as $country)
                                                                                                    <option
                                                                                                        value="{{ $country->code }}"
                                                                                                        {{ $passenger['nationality'] == $country->code ? 'selected' : '' }}>
                                                                                                        {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                                                                                    </option>
                                                                                                @endforeach
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
                                                                                                    value="{{ $passenger['address'] }}"
                                                                                                    placeholder="{{ __('dashboard.Address') }}"
                                                                                                    id="address"
                                                                                                    name="address[]" />
                                                                                                <!-- Hidden gender field with existing value -->
                                                                                                <input type="hidden" name="gender[]" class="existing-gender" value="{{ $passenger['gender'] }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mt-4">
                                                                                   
                                                                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                                   <div class="col-md-12">
                                                                                        <div
                                                                                            class="form-group text-start">
                                                                                            <label class="fw-bold mb-3"
                                                                                                for="grid">{{ __('dashboard.Document Type') }}</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="passport_number">{{ __('dashboard.Passport Number') }}</label>
                                                                                            <input type="number"
                                                                                                required
                                                                                                class="form-control"
                                                                                                value="{{ $passenger['passport_number'] }}"
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
                                                                                                    value="{{ $passenger['passport_expiry_date'] }}"
                                                                                                    name="passport_expiry_date[]"
                                                                                                    placeholder="{{ __('dashboard.Passport Expiry Date') }}"
                                                                                                    min="{{ date('Y-m-d', strtotime('+6 months')) }}">
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
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between pb-0">
                    <div>
                        <button type="button" class="btn btn-danger py-2 px-4 fs-6" data-bs-dismiss="modal">{{ __('dashboard.Close') }}</button>
                        
                    </div>
                    <button type="submit" id="savePassengerBtn"
                                        class="btn btn-warning py-2 px-4 fs-6">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
                                        <span class="btn-text">{{ __('dashboard.Save') }}</span>
                                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to calculate age from birth date
    function calculateAge(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        
        return age;
    }

    // Function to validate name fields
    function validateName(nameInput, fieldType) {
        const name = nameInput.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Remove existing error message
        const existingError = nameInput.parentNode.querySelector('.name-error');
        if (existingError) {
            existingError.remove();
        }
        nameInput.style.borderColor = '';

        // Check minimum length
        if (name.length < 2) {
            isValid = false;
            errorMessage = fieldType + ' must be at least 2 characters long';
        }
        // Check for numbers
        else if (/\d/.test(name)) {
            isValid = false;
            errorMessage = fieldType + ' cannot contain numbers';
        }
        // Check for special characters (only allow letters, spaces, hyphens, apostrophes, and dots)
        else if (!/^[a-zA-Z\s\-\'\.]+$/.test(name)) {
            isValid = false;
            errorMessage = fieldType + ' may only contain letters, spaces, hyphens, apostrophes, and dots';
        }

        // Display error if validation failed
        if (!isValid) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'name-error text-danger mt-1';
            errorDiv.style.fontSize = '12px';
            errorDiv.textContent = errorMessage;
            
            nameInput.parentNode.appendChild(errorDiv);
            nameInput.style.borderColor = '#dc3545';
        }

        return isValid;
    }

    // Function to check for duplicate full names
    function checkDuplicateNames() {
        const firstNameInputs = document.querySelectorAll('input[name="first_name[]"]');
        const lastNameInputs = document.querySelectorAll('input[name="last_name[]"]');
        const fullNames = [];
        let hasDuplicates = false;

        // Clear existing duplicate errors
        document.querySelectorAll('.duplicate-error').forEach(error => error.remove());

        // Collect all full names
        firstNameInputs.forEach((firstInput, index) => {
            const lastInput = lastNameInputs[index];
            if (firstInput && lastInput) {
                const firstName = firstInput.value.trim().toLowerCase();
                const lastName = lastInput.value.trim().toLowerCase();
                const fullName = firstName + ' ' + lastName;
                
                if (firstName && lastName) {
                    fullNames.push({
                        fullName: fullName,
                        firstInput: firstInput,
                        lastInput: lastInput,
                        index: index
                    });
                }
            }
        });

        // Check for duplicates
        fullNames.forEach((nameObj, i) => {
            const duplicates = fullNames.filter((other, j) => 
                i !== j && other.fullName === nameObj.fullName
            );

            if (duplicates.length > 0) {
                hasDuplicates = true;
                
                // Mark both first and last name inputs as invalid
                [nameObj.firstInput, nameObj.lastInput].forEach(input => {
                    input.style.borderColor = '#dc3545';
                    
                    // Add error message if not already present
                    const existingError = input.parentNode.querySelector('.duplicate-error');
                    if (!existingError) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'duplicate-error text-danger mt-1';
                        errorDiv.style.fontSize = '12px';
                        errorDiv.textContent = 'Duplicate passenger full name. Each passenger must have a unique full name.';
                        input.parentNode.appendChild(errorDiv);
                    }
                });
            }
        });

        return !hasDuplicates;
    }

    // Function to validate adult age
    function validateAdultAge(birthDateInput, index) {
        const birthDate = birthDateInput.value;
        
        if (!birthDate) return true; // Skip validation if no date is entered
        
        const age = calculateAge(birthDate);
        
        // Check if age is between 19 and 160 for adults
        if (age < 19 || age > 160) {
            // Remove existing error message if any
            const existingError = birthDateInput.parentNode.querySelector('.age-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Create and show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'age-error text-danger mt-1';
            errorDiv.style.fontSize = '12px';
            errorDiv.textContent = age < 19 ? 
                'Adult must be at least 19 years old.' : 
                'Adult age cannot exceed 160 years. Please check the birth date.';
            
            birthDateInput.parentNode.appendChild(errorDiv);
            birthDateInput.style.borderColor = '#dc3545';
            
            return false;
        } else {
            // Remove error message if age is valid
            const existingError = birthDateInput.parentNode.querySelector('.age-error');
            if (existingError) {
                existingError.remove();
            }
            birthDateInput.style.borderColor = '';
            
            return true;
        }
    }

    // Function to validate child age
    function validateChildAge(birthDateInput, index) {
        const birthDate = birthDateInput.value;
        
        if (!birthDate) return true; // Skip validation if no date is entered
        
        const age = calculateAge(birthDate);
        
        // Check if age is between 1 and 18 for children
        if (age < 1 || age > 18) {
            // Remove existing error message if any
            const existingError = birthDateInput.parentNode.querySelector('.age-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Create and show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'age-error text-danger mt-1';
            errorDiv.style.fontSize = '12px';
            errorDiv.textContent = age < 1 ? 
                'Child must be at least 1 year old.' : 
                'Child age cannot exceed 18 years. Please check the birth date.';
            
            birthDateInput.parentNode.appendChild(errorDiv);
            birthDateInput.style.borderColor = '#dc3545';
            
            return false;
        } else {
            // Remove error message if age is valid
            const existingError = birthDateInput.parentNode.querySelector('.age-error');
            if (existingError) {
                existingError.remove();
            }
            birthDateInput.style.borderColor = '';
            
            return true;
        }
    }

    // Function to validate infant age
    function validateInfantAge(birthDateInput, index) {
        const birthDate = birthDateInput.value;
        
        if (!birthDate) return true; // Skip validation if no date is entered
        
        const age = calculateAge(birthDate);
        
        // Check if age is between 0 and 2 for infants
        if (age < 0 || age > 2) {
            // Remove existing error message if any
            const existingError = birthDateInput.parentNode.querySelector('.age-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Create and show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'age-error text-danger mt-1';
            errorDiv.style.fontSize = '12px';
            errorDiv.textContent = age < 0 ? 
                'Invalid birth date.' : 
                'Infant age cannot exceed 2 years. Please check the birth date.';
            
            birthDateInput.parentNode.appendChild(errorDiv);
            birthDateInput.style.borderColor = '#dc3545';
            
            return false;
        } else {
            // Remove error message if age is valid
            const existingError = birthDateInput.parentNode.querySelector('.age-error');
            if (existingError) {
                existingError.remove();
            }
            birthDateInput.style.borderColor = '';
            
            return true;
        }
    }

    // Set up validation for all birth date inputs using CSS classes
    
    // Adult birth date inputs
    const adultBirthInputs = document.querySelectorAll('.adult-birth-date');
    adultBirthInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            validateAdultAge(this, index);
        });
        
        input.addEventListener('blur', function() {
            validateAdultAge(this, index);
        });
    });

    // Child birth date inputs
    const childBirthInputs = document.querySelectorAll('.child-birth-date');
    childBirthInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            validateChildAge(this, index);
        });
        
        input.addEventListener('blur', function() {
            validateChildAge(this, index);
        });
    });

    // Infant birth date inputs
    const infantBirthInputs = document.querySelectorAll('.infant-birth-date');
    infantBirthInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            validateInfantAge(this, index);
        });
        
        input.addEventListener('blur', function() {
            validateInfantAge(this, index);
        });
    });

    // Set up validation for name inputs
    const firstNameInputs = document.querySelectorAll('input[name="first_name[]"]');
    const lastNameInputs = document.querySelectorAll('input[name="last_name[]"]');

    // First name validation
    firstNameInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            validateName(this, 'First name');
            // Check for duplicates after each input
            setTimeout(() => checkDuplicateNames(), 100);
        });
        
        input.addEventListener('blur', function() {
            validateName(this, 'First name');
            checkDuplicateNames();
        });
    });

    // Last name validation  
    lastNameInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            validateName(this, 'Last name');
            // Check for duplicates after each input
            setTimeout(() => checkDuplicateNames(), 100);
        });
        
        input.addEventListener('blur', function() {
            validateName(this, 'Last name');
            checkDuplicateNames();
        });
    });

    // Validate on form submission
    const form = document.querySelector('#passengerForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Always prevent default form submission
            
            let hasErrors = false;
            let errorMessages = [];
            
            // Clear previous validation errors
            hideValidationErrors();
            hideSuccessMessage();
            
            // Remove all existing validation classes
            const allInputs = form.querySelectorAll('.form-control');
            allInputs.forEach(input => {
                input.classList.remove('is-invalid');
                // Remove any existing error messages
                const existingError = input.parentNode.querySelector('.validation-error');
                if (existingError) {
                    existingError.remove();
                }
            });
            
            // Validate all adult birth dates
            adultBirthInputs.forEach((input, index) => {
                if (!validateAdultAge(input, index)) {
                    hasErrors = true;
                }
            });
            
            // Validate all child birth dates
            childBirthInputs.forEach((input, index) => {
                if (!validateChildAge(input, index)) {
                    hasErrors = true;
                }
            });
            
            // Validate all infant birth dates
            infantBirthInputs.forEach((input, index) => {
                if (!validateInfantAge(input, index)) {
                    hasErrors = true;
                }
            });
            
            // Validate all name fields
            firstNameInputs.forEach((input, index) => {
                if (!validateName(input, 'First name')) {
                    hasErrors = true;
                }
            });
            
            lastNameInputs.forEach((input, index) => {
                if (!validateName(input, 'Last name')) {
                    hasErrors = true;
                }
            });
            
            // Check for duplicate names
            if (!checkDuplicateNames()) {
                hasErrors = true;
            }
            
            // Comprehensive field validation
            const requiredFields = form.querySelectorAll('input[required], select[required]');
            requiredFields.forEach(field => {
                const fieldValue = field.value.trim();
                const fieldName = field.name;
                const fieldType = field.type;
                
                // Check if field is empty
                if (!fieldValue) {
                    hasErrors = true;
                    field.classList.add('is-invalid');
                    showFieldError(field, `This field is required`);
                    return;
                }
                
                // Validate specific field types
                switch (fieldName) {
                    case 'first_name[]':
                    case 'last_name[]':
                        // Name validation is handled by dedicated validateName function above
                        // Skip validation here to avoid duplicate error messages
                        break;
                        
                    case 'email[]':
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(fieldValue)) {
                            hasErrors = true;
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Please enter a valid email address');
                        }
                        break;
                        
                    case 'phone[]':
                        // Check if phone number is between 8-15 digits
                        if (!/^\d{8,15}$/.test(fieldValue)) {
                            hasErrors = true;
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Phone number must be 8-15 digits');
                        }
                        break;
                        
                    case 'passport_number[]':
                        // Passport number validation: 6-15 characters (letters and numbers)
                        if (fieldValue.length < 6 || fieldValue.length > 15) {
                            hasErrors = true;
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Passport number must be 6-15 characters long');
                        } else if (!/^[A-Za-z0-9]+$/.test(fieldValue)) {
                            hasErrors = true;
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Passport number should contain only letters and numbers');
                        }
                        break;
                        
                    case 'passport_expiry_date[]':
                        // Check if passport expiry date is in the future
                        const expiryDate = new Date(fieldValue);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0); // Reset time for accurate comparison
                        
                        if (expiryDate <= today) {
                            hasErrors = true;
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Passport expiry date must be in the future');
                        }
                        
                        // Check if expiry date is not too far in the future (more than 15 years)
                        const maxExpiryDate = new Date();
                        maxExpiryDate.setFullYear(maxExpiryDate.getFullYear() + 15);
                        
                        if (expiryDate > maxExpiryDate) {
                            hasErrors = true;
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Passport expiry date seems too far in the future');
                        }
                        break;
                        
                    case 'address[]':
                        if (fieldValue.length < 5) {
                            hasErrors = true;
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Address must be at least 5 characters long');
                        }
                        break;
                        
                    case 'birth_date[]':
                        // Birth date validation is already handled by age validation functions
                        const birthDate = new Date(fieldValue);
                        const currentDate = new Date();
                        
                        if (birthDate > currentDate) {
                            hasErrors = true;
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Birth date cannot be in the future');
                        }
                        break;
                }
            });
            
            // Validate nationality and gender selects
            const selectFields = form.querySelectorAll('select[required]');
            selectFields.forEach(select => {
                if (!select.value || select.value === '') {
                    hasErrors = true;
                    select.classList.add('is-invalid');
                    showFieldError(select, 'Please select an option');
                }
            });
            
            if (hasErrors) {
                // Collect all error messages
                const allErrors = form.querySelectorAll('.validation-error');
                const errorTexts = Array.from(allErrors).map(error => error.textContent);
                
                // Show summary of validation errors
                const summaryErrors = ['Please correct the following errors:'];
                summaryErrors.push(...errorTexts);
                showValidationErrors(summaryErrors);
                
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }
            
            // If no validation errors, proceed with AJAX submission
            submitFormViaAjax(this);
        });
    }

    // Function to show field-specific error message
    function showFieldError(field, message) {
        // Remove existing error message if any
        const existingError = field.parentNode.querySelector('.validation-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Create and show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error text-danger mt-1';
        errorDiv.style.fontSize = '12px';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }

    // Function to hide validation errors
    function hideValidationErrors() {
        const errorContainer = document.getElementById('validation-errors');
        errorContainer.style.display = 'none';
        
        // Remove is-invalid class from all form controls
        const invalidFields = document.querySelectorAll('.form-control.is-invalid');
        invalidFields.forEach(function(field) {
            field.classList.remove('is-invalid');
        });
        
        // Remove all field-specific error messages
        const fieldErrors = document.querySelectorAll('.validation-error');
        fieldErrors.forEach(function(error) {
            error.remove();
        });
    }

    // Function to show success message
    function showSuccessMessage(message) {
        const successContainer = document.getElementById('success-message');
        const successText = document.getElementById('success-text');
        
        successText.textContent = message;
        successContainer.style.display = 'block';
        successContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Function to hide success message
    function hideSuccessMessage() {
        const successContainer = document.getElementById('success-message');
        successContainer.style.display = 'none';
    }

    // Function to set loading state
    function setLoadingState(isLoading) {
        const submitBtn = document.getElementById('savePassengerBtn');
        const spinner = submitBtn.querySelector('.spinner-border');
        const btnText = submitBtn.querySelector('.btn-text');
        
        if (isLoading) {
            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Saving...';
        } else {
            submitBtn.disabled = false;
            spinner.style.display = 'none';
            btnText.textContent = '{{ __('dashboard.Save') }}';
        }
    }

    // Function to submit form via AJAX
    function submitFormViaAjax(form) {
        setLoadingState(true);
        
        const formData = new FormData(form);
        
        fetch('/passenger-information', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json().catch(() => {
                    // If response is not JSON (redirect), it means success
                    return { success: true };
                });
            } else if (response.status === 422) {
                // Validation errors
                return response.json();
            } else if (response.status === 419) {
                // CSRF token mismatch - refresh the page
                location.reload();
                return;
            } else {
                throw new Error('Network response was not ok');
            }
        })
        .then(data => {
            setLoadingState(false);
            
            if (data.errors) {
                // Display validation errors
                const errors = Object.values(data.errors).flat();
                showValidationErrors(errors);
                
                // Force restore form data after validation errors
                setTimeout(() => {
                    restoreFormData();
                    console.log('Force restoring form data after validation errors');
                }, 100);
            } else {
                // Success - show success message and close modal after delay
                showSuccessMessage('Passengers saved successfully!');
                
                // Mark form as successfully submitted so we can clear saved data
                window.formSubmittedSuccessfully = true;
                
                setTimeout(() => {
                    // Close modal and reload page to update passenger table
                    const modal = bootstrap.Modal.getInstance(document.getElementById('staticBackdrop'));
                    if (modal) {
                        modal.hide();
                    }
                    location.reload();
                }, 1500);
            }
        })
        .catch(error => {
            setLoadingState(false);
            console.error('Error:', error);
            showValidationErrors(['An error occurred while saving passengers. Please try again.']);
        });
    }

    // Function to set gender based on title
    function setGenderFromTitle(titleSelect) {
        const titleValue = titleSelect.value;
        const container = titleSelect.closest('.collapse-div, .card');
        
        // Find the corresponding gender hidden field
        let genderField = container.querySelector('input[name="gender[]"]');
        
        if (genderField) {
            // Set gender based on title:
            // 0 = Mr/Master -> Male (1)
            // 1 = Miss -> Female (2) 
            // 2 = Mrs -> Female (2)
            if (titleValue === '0') {
                genderField.value = '1'; // Male
            } else if (titleValue === '1' || titleValue === '2') {
                genderField.value = '2'; // Female
            }
        }
    }

    // Set up title change listeners for automatic gender assignment
    const titleSelects = document.querySelectorAll('.title-select');
    titleSelects.forEach(function(titleSelect) {
        // Set initial gender based on current title value
        setGenderFromTitle(titleSelect);
        
        // Listen for title changes
        titleSelect.addEventListener('change', function() {
            setGenderFromTitle(this);
        });
    });

    // Real-time validation as user types
    function addRealTimeValidation() {
        // Track which fields have been touched by the user
        const touchedFields = new Set();
        
        // Name validation (first name, last name)
        const nameInputs = form.querySelectorAll('input[name="first_name[]"], input[name="last_name[]"]');
        nameInputs.forEach(input => {
            // Mark field as touched when user focuses on it
            input.addEventListener('focus', function() {
                touchedFields.add(this);
            });
            
            input.addEventListener('input', function() {
                const value = this.value.trim();
                
                // Remove existing error
                this.classList.remove('is-invalid');
                const existingError = this.parentNode.querySelector('.validation-error');
                if (existingError) existingError.remove();
                
                // Only show validation errors if the field has been touched
                if (touchedFields.has(this) && value.length > 0) {
                    if (value.length < 2) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Name must be at least 2 characters long');
                    } else if (!/^[a-zA-Z\s\u0600-\u06FF]+$/.test(value)) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Name should contain only letters');
                    }
                }
            });
        });
        
        // Email validation
        const emailInputs = form.querySelectorAll('input[name="email[]"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', function() {
                const value = this.value.trim();
                
                // Remove existing error
                this.classList.remove('is-invalid');
                const existingError = this.parentNode.querySelector('.validation-error');
                if (existingError) existingError.remove();
                
                if (value.length > 0) {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(value)) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Please enter a valid email address');
                    }
                }
            });
        });
        
        // Phone validation
        const phoneInputs = form.querySelectorAll('input[name="phone[]"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                const value = this.value.trim();
                
                // Remove existing error
                this.classList.remove('is-invalid');
                const existingError = this.parentNode.querySelector('.validation-error');
                if (existingError) existingError.remove();
                
                if (value.length > 0) {
                    // Remove non-numeric characters for display
                    this.value = value.replace(/\D/g, '');
                    
                    if (this.value.length > 0 && (this.value.length < 8 || this.value.length > 15)) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Phone number must be 8-15 digits');
                    }
                }
            });
        });
        
        // Passport number validation
        const passportInputs = form.querySelectorAll('input[name="passport_number[]"]');
        passportInputs.forEach(input => {
            input.addEventListener('input', function() {
                const value = this.value.trim();
                
                // Remove existing error
                this.classList.remove('is-invalid');
                const existingError = this.parentNode.querySelector('.validation-error');
                if (existingError) existingError.remove();
                
                if (value.length > 0) {
                    // Convert to uppercase for passport numbers
                    this.value = value.toUpperCase();
                    
                    if (this.value.length < 6 || this.value.length > 15) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Passport number must be 6-15 characters long');
                    } else if (!/^[A-Z0-9]+$/.test(this.value)) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Passport number should contain only letters and numbers');
                    }
                }
            });
        });
        
        // Passport expiry date validation
        const passportExpiryInputs = form.querySelectorAll('input[name="passport_expiry_date[]"]');
        passportExpiryInputs.forEach(input => {
            input.addEventListener('change', function() {
                const value = this.value;
                
                // Remove existing error
                this.classList.remove('is-invalid');
                const existingError = this.parentNode.querySelector('.validation-error');
                if (existingError) existingError.remove();
                
                if (value) {
                    const expiryDate = new Date(value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (expiryDate <= today) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Passport expiry date must be in the future');
                    } else {
                        const maxExpiryDate = new Date();
                        maxExpiryDate.setFullYear(maxExpiryDate.getFullYear() + 15);
                        
                        if (expiryDate > maxExpiryDate) {
                            this.classList.add('is-invalid');
                            showFieldError(this, 'Passport expiry date seems too far in the future');
                        }
                    }
                }
            });
        });
        
        // Address validation
        const addressInputs = form.querySelectorAll('input[name="address[]"]');
        addressInputs.forEach(input => {
            input.addEventListener('blur', function() {
                const value = this.value.trim();
                
                // Remove existing error
                this.classList.remove('is-invalid');
                const existingError = this.parentNode.querySelector('.validation-error');
                if (existingError) existingError.remove();
                
                if (value.length > 0 && value.length < 5) {
                    this.classList.add('is-invalid');
                    showFieldError(this, 'Address must be at least 5 characters long');
                }
            });
        });
        
        // Birth date validation
        const birthDateInputs = form.querySelectorAll('input[name="birth_date[]"]');
        birthDateInputs.forEach(input => {
            input.addEventListener('change', function() {
                const value = this.value;
                
                // Remove existing error (age errors are handled by specific age validation functions)
                const existingError = this.parentNode.querySelector('.validation-error');
                if (existingError && !existingError.classList.contains('age-error')) {
                    existingError.remove();
                }
                
                if (value) {
                    const birthDate = new Date(value);
                    const currentDate = new Date();
                    
                    if (birthDate > currentDate) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Birth date cannot be in the future');
                    }
                }
            });
        });
        
        // Select field validation
        const selectFields = form.querySelectorAll('select[required]');
        selectFields.forEach(select => {
            select.addEventListener('change', function() {
                // Remove existing error
                this.classList.remove('is-invalid');
                const existingError = this.parentNode.querySelector('.validation-error');
                if (existingError) existingError.remove();
                
                if (this.value === '' || !this.value) {
                    this.classList.add('is-invalid');
                    showFieldError(this, 'Please select an option');
                }
            });
        });
    }

    // Clear validation errors when user starts typing
    const formInputs = document.querySelectorAll('#passengerForm input, #passengerForm select');
    formInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            // Hide main validation errors if they exist and no fields have errors
            const hasAnyErrors = document.querySelector('.form-control.is-invalid');
            if (!hasAnyErrors) {
                hideValidationErrors();
            }
        });
        
        input.addEventListener('change', function() {
            // Hide main validation errors if they exist and no fields have errors  
            const hasAnyErrors = document.querySelector('.form-control.is-invalid');
            if (!hasAnyErrors) {
                hideValidationErrors();
            }
        });
    });

    // Initialize real-time validation
    if (form) {
        addRealTimeValidation();
    }

    // Enhanced error display function that adds visual indicators to invalid fields
    function showValidationErrors(errors) {
        const errorContainer = document.getElementById('validation-errors');
        const errorList = document.getElementById('error-list');
        
        errorList.innerHTML = '';
        errors.forEach(function(error) {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
        
        errorContainer.style.display = 'block';
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Add visual indicators to invalid fields based on error messages
        errors.forEach(function(error) {
            addInvalidClassToFields(error);
        });
    }

                }
        });
    }

    // Form data persistence functions
    function saveFormData() {
        const formData = {};
        const form = document.getElementById('passengerForm');
        
        if (form) {
            const formDataObj = new FormData(form);
            
            // Save text inputs, emails, phones, addresses
            const textInputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], input[type="address"], input[type="date"]');
            textInputs.forEach(input => {
                if (input.name && input.value) {
                    if (!formData[input.name]) formData[input.name] = [];
                    formData[input.name].push(input.value);
                }
            });
            
            // Save select values
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                if (select.name && select.value) {
                    if (!formData[select.name]) formData[select.name] = [];
                    formData[select.name].push(select.value);
                }
            });
            
            // Save hidden fields
            const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
            hiddenInputs.forEach(input => {
                if (input.name && input.value) {
                    if (!formData[input.name]) formData[input.name] = [];
                    formData[input.name].push(input.value);
                }
            });
            
            localStorage.setItem('passengerFormData', JSON.stringify(formData));
            sessionStorage.setItem('passengerFormData', JSON.stringify(formData));
            console.log('Form data saved:', formData); // Debug log
        }
    }
    
    function restoreFormData() {
        const savedData = localStorage.getItem('passengerFormData') || sessionStorage.getItem('passengerFormData');
        console.log('Attempting to restore form data:', savedData); // Debug log
        
        if (!savedData) {
            console.log('No saved data found');
            return;
        }
        
        try {
            const formData = JSON.parse(savedData);
            const form = document.getElementById('passengerForm');
            
            if (form) {
                console.log('Restoring form data:', formData); // Debug log
                console.log('Form found:', form); // Debug log
                
                // Log all form inputs to see what's available
                const allInputs = form.querySelectorAll('input, select');
                console.log('All form inputs found:', allInputs.length);
                allInputs.forEach((input, index) => {
                    console.log(`Input ${index}: name="${input.name}", type="${input.type}", id="${input.id}"`);
                });
                
                // Restore text inputs, emails, phones, addresses
                const textInputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="number"], input[type="address"], input[type="date"]');
                let inputIndex = {};
                
                textInputs.forEach(input => {
                    if (input.name && formData[input.name]) {
                        if (!inputIndex[input.name]) inputIndex[input.name] = 0;
                        if (formData[input.name][inputIndex[input.name]]) {
                            input.value = formData[input.name][inputIndex[input.name]];
                            console.log(`Restored ${input.name}[${inputIndex[input.name]}]:`, input.value); // Debug log
                        }
                        inputIndex[input.name]++;
                    }
                });
                
                // Restore select values
                const selects = form.querySelectorAll('select');
                let selectIndex = {};
                
                selects.forEach(select => {
                    if (select.name && formData[select.name]) {
                        if (!selectIndex[select.name]) selectIndex[select.name] = 0;
                        if (formData[select.name][selectIndex[select.name]]) {
                            select.value = formData[select.name][selectIndex[select.name]];
                            console.log(`Restored ${select.name}[${selectIndex[select.name]}]:`, select.value); // Debug log
                            
                            // Trigger change event to update gender if it's a title select
                            if (select.classList.contains('title-select')) {
                                setGenderFromTitle(select);
                            }
                        }
                        selectIndex[select.name]++;
                    }
                });
                
                // Restore hidden fields
                const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
                let hiddenIndex = {};
                
                hiddenInputs.forEach(input => {
                    if (input.name && formData[input.name]) {
                        if (!hiddenIndex[input.name]) hiddenIndex[input.name] = 0;
                        if (formData[input.name][hiddenIndex[input.name]]) {
                            input.value = formData[input.name][hiddenIndex[input.name]];
                            console.log(`Restored ${input.name}[${hiddenIndex[input.name]}]:`, input.value); // Debug log
                        }
                        hiddenIndex[input.name]++;
                    }
                });
            }
        } catch (error) {
            console.warn('Error restoring form data:', error);
            localStorage.removeItem('passengerFormData');
        }
    }
    
    function clearSavedFormData() {
        localStorage.removeItem('passengerFormData');
        sessionStorage.removeItem('passengerFormData');
    }
    
    // Auto-save form data as user types
    function setupAutoSave() {
        const form = document.getElementById('passengerForm');
        if (form) {
            const indicator = document.getElementById('data-persistence-indicator');
            let saveTimeout;
            
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    // Show indicator
                    if (indicator) {
                        indicator.style.display = 'block';
                        document.getElementById('persistence-text').textContent = 'Saving your data...';
                    }
                    
                    // Clear previous timeout
                    clearTimeout(saveTimeout);
                    
                    // Save after a short delay
                    saveTimeout = setTimeout(() => {
                        saveFormData();
                        if (indicator) {
                            document.getElementById('persistence-text').textContent = 'Your form data is automatically saved as you type.';
                        }
                    }, 500);
                });
                
                input.addEventListener('change', function() {
                    saveFormData();
                    if (indicator) {
                        indicator.style.display = 'block';
                        document.getElementById('persistence-text').textContent = 'Data saved successfully.';
                        
                        // Hide indicator after 2 seconds
                        setTimeout(() => {
                            indicator.style.display = 'none';
                        }, 2000);
                    }
                });
            });
            
            // Also save form data before submission to ensure it's captured
            form.addEventListener('submit', function() {
                saveFormData();
                console.log('Form data saved before submission');
            });
        }
    }

    // Modal event listeners for cleanup
    const modal = document.getElementById('staticBackdrop');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            // Clear validation errors and success messages when modal is closed
            hideValidationErrors();
            hideSuccessMessage();
            setLoadingState(false);
            
            // Only clear saved data if form was successfully submitted
            // (This will be set by the success handler)
            if (window.formSubmittedSuccessfully) {
                clearSavedFormData();
                window.formSubmittedSuccessfully = false;
            }
        });
        
        modal.addEventListener('show.bs.modal', function () {
            // Clear any previous messages when modal is opened
            hideValidationErrors();
            hideSuccessMessage();
            
            // Clear any validation states from previous submissions
            const allFormControls = form.querySelectorAll('.form-control');
            allFormControls.forEach(input => {
                input.classList.remove('is-invalid');
                const existingError = input.parentNode.querySelector('.validation-error');
                if (existingError) existingError.remove();
            });
            
            // Wait for modal to fully open before restoring data
            setTimeout(() => {
                // Check if there's saved data and show indicator
                const savedData = localStorage.getItem('passengerFormData') || sessionStorage.getItem('passengerFormData');
                const indicator = document.getElementById('data-persistence-indicator');
                
                if (savedData && indicator) {
                    indicator.style.display = 'block';
                    document.getElementById('persistence-text').textContent = 'Restoring your previously entered data...';
                    
                    // Restore form data if available
                    restoreFormData();
                    
                    setTimeout(() => {
                        document.getElementById('persistence-text').textContent = 'Your form data is automatically saved as you type.';
                    }, 1000);
                } else {
                    // Restore form data even if indicator doesn't exist
                    restoreFormData();
                }
                
                // Setup auto-save functionality
                setupAutoSave();
            }, 100); // Small delay to ensure DOM is ready
            
            // Setup clear form button
            const clearFormBtn = document.getElementById('clearFormBtn');
            if (clearFormBtn) {
                // Remove any existing event listeners to prevent duplicates
                clearFormBtn.replaceWith(clearFormBtn.cloneNode(true));
                const newClearFormBtn = document.getElementById('clearFormBtn');
                
                newClearFormBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to clear all form data? This action cannot be undone.')) {
                        // Clear the form
                        const form = document.getElementById('passengerForm');
                        if (form) {
                            form.reset();
                        }
                        
                        // Clear saved data
                        clearSavedFormData();
                        
                        // Hide persistence indicator
                        const indicator = document.getElementById('data-persistence-indicator');
                        if (indicator) {
                            indicator.style.display = 'none';
                        }
                        
                        // Clear validation errors
                        hideValidationErrors();
                        hideSuccessMessage();
                        
                        // Remove validation classes
                        const allInputs = form.querySelectorAll('.form-control');
                        allInputs.forEach(input => {
                            input.classList.remove('is-invalid', 'is-valid');
                        });
                    }
                });
            }
        });
    }

    // Function to add invalid class to fields based on error message
    function addInvalidClassToFields(errorMessage) {
        const fieldMapping = {
            'first_name': ['first name', 'الاسم الأول'],
            'last_name': ['last name', 'الاسم الأخير'],
            'email': ['email', 'الإيميل', 'البريد'],
            'phone': ['phone', 'الهاتف', 'رقم الهاتف'],
            'birth_date': ['birth date', 'تاريخ الميلاد'],
            'nationality': ['nationality', 'الجنسية'],
            'address': ['address', 'العنوان'],
            'passport_number': ['passport number', 'رقم الجواز'],
            'passport_expiry_date': ['passport expiry', 'انتهاء الجواز']
        };
        
        Object.keys(fieldMapping).forEach(function(fieldName) {
            const keywords = fieldMapping[fieldName];
            const matchesKeyword = keywords.some(keyword => 
                errorMessage.toLowerCase().includes(keyword.toLowerCase())
            );
            
            if (matchesKeyword) {
                const fields = document.querySelectorAll(`input[name="${fieldName}[]"], select[name="${fieldName}[]"]`);
                fields.forEach(function(field) {
                    field.classList.add('is-invalid');
                });
            }
        });
    }
});
</script>

<style>
    #validation-errors {
        border-radius: 8px;
        border: 1px solid #dc3545;
    }
    
    #success-message {
        border-radius: 8px;
        border: 1px solid #28a745;
    }
    
    #data-persistence-indicator {
        border-radius: 8px;
        border: 1px solid #17a2b8;
        background-color: #d1ecf1;
        color: #0c5460;
        font-size: 14px;
        padding: 10px 15px;
        transition: opacity 0.3s ease-in-out;
    }
    
    #data-persistence-indicator i {
        margin-right: 8px;
        color: #17a2b8;
    }
    
    #validation-errors ul {
        list-style-type: disc;
        padding-left: 20px;
    }
    
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
    
    .age-error {
        font-size: 12px;
        margin-top: 5px;
    }
    
    .validation-error {
        font-size: 12px;
        margin-top: 5px;
        color: #dc3545;
        font-weight: 500;
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    /* Success state for valid fields */
    .form-control.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
      .select2-dropdown--below{
            z-index: 9999 !important;
        }
    /* Smooth transitions for better UX */
    .form-control {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    /* Better styling for error containers */
    #validation-errors ul li {
        margin-bottom: 5px;
    }
    
    /* Loading button state */
    #savePassengerBtn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
