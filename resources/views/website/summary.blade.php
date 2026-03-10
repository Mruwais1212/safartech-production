@extends('website.layout')
@section('title', __('dashboard.summary'))
@section('content')
    <!-- Header Start -->
    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 2]))
        <div style="background-image: url('/site/img/summary.png');background-position-y:center ;"
            class="container-fluid header bg-white">
        @else
            <div style="background-image: url('/site/img/image.jpg');background-position-y:center ;"
                class="container-fluid header bg-white">
    @endif

    <div class="background without-waves"></div> <!-- div of shadow and waves -->

    <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
        <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
            <div class="container">
                <div class="row d-flex align-items-center justify-content-center">
                    <div class="col-md-6 main-header-col">
                        <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.summary') }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Header End -->

    <!-- steps form -->
    <div class="container last-step">
        <div class="row">
            <div class="col-md-12">
                <form action="/payment" class="steps-form" method="post">
                    @csrf
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="progress-container text-center">
                                    <div class="progress" id="progress" style="width:90%;"></div>

                                    <div class="text-wrap finished">
                                        <div class="circle">01</div>
                                        <p class="text">{{ __('dashboard.Plan') }}</p>
                                    </div>
                                    <div class="text-wrap finished">
                                        <div class="circle">02</div>
                                        <p class="text">{{ __('dashboard.Compare') }}</p>
                                    </div>
                                    <div class="text-wrap finished">
                                        <div class="circle">03</div>
                                        <p class="text">{{ __('dashboard.Select') }}</p>
                                    </div>
                                    <div class="text-wrap active">
                                        <div class="circle">04</div>
                                        <p class="text">{{ __('dashboard.Payment') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="plan-section">

                                            <div class="header-summery">
                                                <h3 class="text-warning">{{ session('preferences')['destination_name'] }}
                                                </h3>
                                            </div>

                                            {{-- <div class="plan-gallery">
                                                <div class="container p-0">
                                                    <div class="row justify-content-start">
                                                        <div class="col col-md-12 gallery-container-wrap position-relative">
                                                            <button type="button" class="btn btn-dynamic d-none"
                                                                id="dynamic-mode-images"></button>
                                                            <div class="gallery-container" id="gallery-dynamic-thumbnails">
                                                                <a class="gallery-item w-100" data-index="0"
                                                                    data-src="img/gallery1.png">
                                                                    <img alt="layers of blue." class="img-responsive"
                                                                        src="img/gallery1.png" />
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}

                                            <hr>
                                            <div class="plan-details">
                                                <div class="plan-details-title">
                                                    <h6>{{ __('dashboard.Traveling details') }}</h6>
                                                </div>
                                                <p class="details">
                                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1]))
                                                        {{ __('dashboard.this is a flight and hotel trip') }}
                                                        {{ __('dashboard.from') }}
                                                        {{ session('preferences')['origin_name'] }}
                                                        {{ __('dashboard.to') }}
                                                        {{ session('preferences')['destination_name'] }}
                                                    @endif

                                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [2]))
                                                        {{ __('dashboard.this is hotel booking only') }}
                                                        {{ __('dashboard.to') }}
                                                        {{ session('preferences')['destination_name'] }}
                                                    @endif

                                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [3]))
                                                        {{ __('dashboard.this is a flight booking only') }}
                                                        {{ __('dashboard.from') }}
                                                        {{ session('preferences')['origin_name'] }}
                                                        {{ __('dashboard.to') }}
                                                        {{ session('preferences')['destination_name'] }}
                                                    @endif

                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <div class="plan-section">

                                            <div class="header-summery">
                                                <h3 class="text-dark">{{ __('dashboard.Passenger Details') }}</h3>
                                                {{-- <div class="summery-options">
                                                    <a href="/passenger-information">{{ __('dashboard.edit') }}</a>
                                                </div> --}}
                                                <button type="button" data-bs-toggle="modal"
                                                    data-bs-target="#staticBackdrop"
                                                    class="btn btn-warning py-3 animated fadeIn">
                                                    {{ __('dashboard.add_edit_passengers') }}</button>
                                            </div>

                                            <hr>
                                            <div class="plan-details">
                                                <div class="plan-details-title">
                                                    <h6>{{ __('dashboard.Traveling details') }}</h6>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered passenger-table ">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-warning" scope="col">
                                                                    {{ __('dashboard.Name') }}</th>
                                                                @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                    <th class="text-warning" scope="col">
                                                                        {{ __('dashboard.Passport') }}</th>
                                                                @endif
                                                                <th class="text-warning" scope="col">
                                                                    {{ __('dashboard.Phone Number') }}</th>
                                                                <th class="text-warning" scope="col">
                                                                    {{ __('dashboard.Gender') }}</th>
                                                                <th class="text-warning" scope="col">
                                                                    {{ __('dashboard.Age') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($passengers as $passenger)
                                                                <tr>
                                                                    <td>{{ @$passenger->first_name }}
                                                                        {{ @$passenger->last_name }}
                                                                    </td>
                                                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                                                        <td>{{ @$passenger->passport_number }}</td>
                                                                    @endif
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

                                    <!-- SSR (Special Service Request) Section -->
                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                        @php
                                            // Get dynamic SSR data from controller
                                            $outboundSSR = session('flight')['SSRDetails']?? [];
                                            $inboundSSR = session('flight')['InboundSSRDetails']?? [];

                                            // Extract meals and baggage from the SSR API response structure
                                            $availableMeals = [];
                                            $availableBaggage = [];
                                            
                                            // Process MealDynamic data - taking first flight segment's meals
                                            if (isset($outboundSSR['MealDynamic']) && is_array($outboundSSR['MealDynamic']) && !empty($outboundSSR['MealDynamic'][0])) {
                                                foreach ($outboundSSR['MealDynamic'][0] as $meal) {
                                                    // Skip "NoMeal" option as we handle it separately
                                                    if (isset($meal['Code']) && $meal['Code'] !== 'NoMeal') {
                                                        $availableMeals[] = [
                                                            'code' => $meal['Code'],
                                                            'description' => $meal['AirlineDescription'] ?? $meal['Code'],
                                                            'price' => $meal['Price'] ?? 0,
                                                            'currency' => $meal['Currency'] ?? 'USD'
                                                        ];
                                                    }
                                                }
                                            }
                                            
                                            // Process Baggage data - taking first flight segment's baggage
                                            if (isset($outboundSSR['Baggage']) && is_array($outboundSSR['Baggage']) && !empty($outboundSSR['Baggage'][0])) {
                                                foreach ($outboundSSR['Baggage'][0] as $baggage) {
                                                    // Skip "NoBaggage" option as we handle it separately
                                                    if (isset($baggage['Code']) && $baggage['Code'] !== 'NoBaggage') {
                                                        $availableBaggage[] = [
                                                            'code' => $baggage['Code'],
                                                            'description' => ($baggage['Weight'] ? $baggage['Weight'] . 'kg ' : '') . 'Extra Baggage',
                                                            'price' => $baggage['Price'] ?? 0,
                                                            'currency' => $baggage['Currency'] ?? 'USD',
                                                            'weight' => $baggage['Weight'] ?? 0
                                                        ];
                                                    } 
                                                }
                                            }
                                            
                                            // Check if we have any SSR data at all
                                            $hasAnySSRData = !empty($availableMeals) || !empty($availableBaggage);
                                            
                                            // Determine if this is an LCC airline based on baggage pricing
                                            $isLcc = false;
                                            if (!empty($availableBaggage)) {
                                                $isLcc = true; // If baggage options are available with prices, likely LCC
                                            }
                                            
                                            // If no meals from API, use fallback options
                                            if (empty($availableMeals)) {
                                                $availableMeals = [
                                                    // ['code' => 'VGML', 'description' => 'Vegetarian Meal', 'price' => $isLcc ? 25 : 0, 'currency' => 'SAR'],
                                                    // ['code' => 'MOML', 'description' => 'Halal Meal', 'price' => $isLcc ? 30 : 0, 'currency' => 'SAR'],
                                                    // ['code' => 'KSML', 'description' => 'Kosher Meal', 'price' => $isLcc ? 35 : 0, 'currency' => 'SAR'],
                                                    // ['code' => 'CHML', 'description' => 'Child Meal', 'price' => $isLcc ? 20 : 0, 'currency' => 'SAR']
                                                ];
                                            }
                                            
                                            // If no baggage from API and it's LCC, use fallback options
                                            if (empty($availableBaggage) && $isLcc) {
                                                $availableBaggage = [
                                                    // ['code' => '5KG', 'description' => 'Extra 5kg Baggage', 'price' => 50, 'currency' => 'SAR', 'weight' => 5],
                                                    // ['code' => '10KG', 'description' => 'Extra 10kg Baggage', 'price' => 90, 'currency' => 'SAR', 'weight' => 10],
                                                    // ['code' => '15KG', 'description' => 'Extra 15kg Baggage', 'price' => 130, 'currency' => 'SAR', 'weight' => 15],
                                                    // ['code' => '20KG', 'description' => 'Extra 20kg Baggage', 'price' => 160, 'currency' => 'SAR', 'weight' => 20]
                                                ];
                                            }
                                            
                                            // Helper function to convert price to SAR
                                            $convertToSAR = function($price, $currency) {
                                                if ($currency === 'USD') {
                                                    return round($price * 3.75, 2); // USD to SAR conversion rate
                                                } elseif ($currency === 'EUR') {
                                                    return round($price * 4.1, 2); // EUR to SAR conversion rate
                                                }
                                                return $price; // Already in SAR or unknown currency
                                            };
                                        @endphp
                                        
                                        <div class="col-md-12 mt-3">
                                            <div class="plan-section">
                                                <div class="header-summery">
                                                    <h3 class="text-dark">{{ __('dashboard.Special Services') }}</h3>
                                                    <small class="text-muted">{{ __('dashboard.Select meals and extra baggage for each passenger') }}</small>
                                                </div>
                                                <hr>
                                                
                                                @if(!$hasAnySSRData)
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>{{ __('dashboard.SSR Session Expired') }}</strong><br>
                                                        {{ __('dashboard.SSR data could not be retrieved from the airline. Standard meal and baggage options are shown below with estimated pricing.') }}
                                                        <br><small>{{ __('dashboard.Final pricing will be confirmed during booking process.') }}</small>
                                                    </div>
                                                @endif
                                                
                                                {{-- @if(config('app.debug'))
                                                    <div class="alert alert-info">
                                                        <strong>SSR Debug Info:</strong><br>
                                                        Airline Type: {{ $isLcc ? 'LCC (Low Cost Carrier)' : 'Non-LCC (Full Service)' }}<br>
                                                        Outbound SSR Available: {{ !empty($outboundSSR) ? 'Yes' : 'No' }}<br>
                                                        Inbound SSR Available: {{ !empty($inboundSSR) ? 'Yes' : 'No' }}<br>
                                                        Available Meals: {{ count($availableMeals) }}<br>
                                                        Available Baggage: {{ count($availableBaggage) }}<br>
                                                        Using Fallback Data: {{ !$hasAnySSRData ? 'Yes' : 'No' }}<br>
                                                        Raw MealDynamic Count: {{ isset($outboundSSR['MealDynamic'][0]) ? count($outboundSSR['MealDynamic'][0]) : 0 }}<br>
                                                        Raw Baggage Count: {{ isset($outboundSSR['Baggage'][0]) ? count($outboundSSR['Baggage'][0]) : 0 }}<br>
                                                        <details>
                                                            <summary>Processed Meals (click to expand)</summary>
                                                            <pre>{{ json_encode($availableMeals, JSON_PRETTY_PRINT) }}</pre>
                                                        </details>
                                                        <details>
                                                            <summary>Processed Baggage (click to expand)</summary>
                                                            <pre>{{ json_encode($availableBaggage, JSON_PRETTY_PRINT) }}</pre>
                                                        </details>
                                                        <details>
                                                            <summary>Raw SSR Data (click to expand)</summary>
                                                            <pre>{{ json_encode($ssrData, JSON_PRETTY_PRINT) }}</pre>
                                                        </details>
                                                    </div>
                                                @endif --}}
                                                
                                                <div class="plan-details">
                                                    <div class="ssr-container">
                                                        @foreach ($passengers as $index => $passenger)
                                                            <div class="passenger-ssr mb-4 p-3 border rounded">
                                                                <h5 class="mb-3">
                                                                    <i class="fas fa-user text-warning me-2"></i>
                                                                    {{ $passenger->first_name }} {{ $passenger->last_name }}
                                                                </h5>
                                                                
                                                                <div class="row">
                                                                    <!-- Meal Selection -->
                                                                    <div class="col-md-6">
                                                                        <h6 class="text-primary mb-3">
                                                                            <i class="fas fa-utensils me-2"></i>
                                                                            {{ __('dashboard.Meal Preference') }}
                                                                        </h6>
                                                                        <div class="meal-options">
                                                                            <div class="form-check mb-2">
                                                                                <input class="form-check-input meal-radio" type="radio" 
                                                                                       name="meal_{{ $index }}" 
                                                                                       id="meal_none_{{ $index }}" 
                                                                                       value="none" 
                                                                                       data-price="0" 
                                                                                       data-passenger="{{ $index }}" 
                                                                                       checked>
                                                                                <label class="form-check-label" for="meal_none_{{ $index }}">
                                                                                    {{ __('dashboard.No Special Meal') }} - <span class="text-success">{{ __('dashboard.Free') }}</span>
                                                                                </label>
                                                                            </div>
                                                                            @foreach($availableMeals as $meal)
                                                                                @php
                                                                                    $mealPriceSAR = $meal['price']; //$convertToSAR($meal['price'], $meal['currency'] ?? 'SAR');
                                                                                @endphp
                                                                                <div class="form-check mb-2">
                                                                                    <input class="form-check-input meal-radio" type="radio" 
                                                                                           name="meal_{{ $index }}" 
                                                                                           id="meal_{{ $meal['code'] }}_{{ $index }}" 
                                                                                           value="{{ $meal['code'] }}" 
                                                                                           data-price="{{ $mealPriceSAR }}" 
                                                                                           data-passenger="{{ $index }}">
                                                                                    <label class="form-check-label" for="meal_{{ $meal['code'] }}_{{ $index }}">
                                                                                        {{ $meal['description'] }} - 
                                                                                        @if($mealPriceSAR > 0)
                                                                                            <span class="text-warning">{{ $mealPriceSAR }} SAR</span>
                                                                                        @else
                                                                                            <span class="text-success">{{ __('dashboard.Free') }}</span>
                                                                                        @endif
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Baggage Selection -->
                                                                    <div class="col-md-6">
                                                                        <h6 class="text-primary mb-3">
                                                                            <i class="fas fa-suitcase me-2"></i>
                                                                            {{ __('dashboard.Extra Baggage') }}
                                                                        </h6>
                                                                        <div class="baggage-options">
                                                                            <div class="form-check mb-2">
                                                                                <input class="form-check-input baggage-radio" type="radio" 
                                                                                       name="baggage_{{ $index }}" 
                                                                                       id="baggage_none_{{ $index }}" 
                                                                                       value="none" 
                                                                                       data-price="0" 
                                                                                       data-passenger="{{ $index }}" 
                                                                                       checked>
                                                                                <label class="form-check-label" for="baggage_none_{{ $index }}">
                                                                                    {{ __('dashboard.Standard Allowance') }} - <span class="text-success">{{ __('dashboard.Included') }}</span>
                                                                                </label>
                                                                            </div>
                                                                            @foreach($availableBaggage as $baggage)
                                                                                @php
                                                                                    $baggagePriceSAR = $baggage['price']; //$convertToSAR($baggage['price'], $baggage['currency'] ?? 'SAR');
                                                                                @endphp
                                                                                <div class="form-check mb-2">
                                                                                    <input class="form-check-input baggage-radio" type="radio" 
                                                                                           name="baggage_{{ $index }}" 
                                                                                           id="baggage_{{ $baggage['code'] }}_{{ $index }}" 
                                                                                           value="{{ $baggage['code'] }}" 
                                                                                           data-price="{{ $baggagePriceSAR }}" 
                                                                                           data-passenger="{{ $index }}">
                                                                                    <label class="form-check-label" for="baggage_{{ $baggage['code'] }}_{{ $index }}">
                                                                                        {{ $baggage['description'] }} - 
                                                                                        @if($baggagePriceSAR > 0)
                                                                                            <span class="text-warning">{{ $baggagePriceSAR }} SAR</span>
                                                                                        @else
                                                                                            <span class="text-success">{{ __('dashboard.Included') }}</span>
                                                                                        @endif
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Passenger SSR Summary -->
                                                                {{-- <div class="row mt-3">
                                                                    <div class="col-12">
                                                                        <div class="alert alert-info passenger-ssr-summary" 
                                                                             id="ssr_summary_{{ $index }}" 
                                                                             style="display: none;">
                                                                            <strong>{{ __('dashboard.Selected Services') }}:</strong>
                                                                            <span class="selected-services"></span>
                                                                            <br>
                                                                            <strong>{{ __('dashboard.Additional Cost') }}:</strong>
                                                                            <span class="additional-cost">0 SAR</span>
                                                                        </div>
                                                                    </div>
                                                                </div> --}}
                                                            </div>
                                                        @endforeach
                                                        
                                                        <!-- Total SSR Cost Summary -->
                                                        <div class="ssr-total-summary mt-4 p-3 bg-light rounded">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <h5 class="text-primary mb-0">
                                                                        <i class="fas fa-calculator me-2"></i>
                                                                        {{ __('dashboard.Total Special Services Cost') }}
                                                                    </h5>
                                                                    <small class="text-muted">{{ __('dashboard.This will be added to your final booking amount') }}</small>
                                                                </div>
                                                                <div class="col-md-4 text-end">
                                                                    <h4 class="text-success mb-0">
                                                                        <span id="total_ssr_cost">0</span> SAR
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 2]))
                                        @php
                                            $hotel_session = session('hotel.selected_hotel');
                                            $selected_hotel = is_string($hotel_session) ? json_decode($hotel_session) : $hotel_session;
                                        @endphp
                                        <div class="hotel-section">
                                        <div class="col-md-12 mt-5">
                                            <div class="plan-section">
                                                <div class="header-summery">
                                                    <h3 class="text-dark">{{ __('dashboard.Hotel Details') }}</h3>
                                                    <div class="summery-options">
                                                        <a class="btn btn-warning py-2 px-5 animated fadeIn fw-normal"
                                                            href="/hotels">{{ __('dashboard.edit') }}</a>
                                                    </div>
                                                </div>
                                                <div class="plan-details hotel-detailss">
                                                    <div class="hotel-details-form">
                                                        <div class="gallery-container position-relative">
                                                            @if(isset($selected_hotel->images[0]))
                                                            <a class="gallery-item w-100" data-index="0"
                                                                data-src="{{ @$selected_hotel->images[0] }}">
                                                                <img alt="layers of blue." class="img-responsive"
                                                                    src="{{ @$selected_hotel->images[0] }}" />
                                                            </a>
                                                            @else
                                                            <a class="gallery-item w-100" data-index="0"
                                                                data-src="/no-image.png">
                                                                <img alt="layers of blue." class="img-responsive"
                                                                    src="/no-image.png" />
                                                            </a>
                                                            @endif
                                                            <div style="opacity: 0" class="top-title">
                                                                <h3>{{ app()->getLocale() == 'en' ? $selected_hotel->name_en : $selected_hotel->name_ar }}
                                                                </h3>
                                                                <p>{{ $selected_hotel->address }}</p>
                                                            </div>
                                                            <div style="opacity: 0" class="bottom-title">
                                                                <h3>{{ __('dashboard.Hotel Roles') }}</h3>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <p>
                                                                        {{ __('dashboard.Check-In') }}
                                                                        {{ $selected_hotel->check_in_time }}
                                                                        {{ __('dashboard.Check-Out') }}
                                                                        {{ $selected_hotel->check_out_time }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered passenger-table ">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-warning" scope="col">
                                                                        {{ app()->getLocale() == 'en' ? $selected_hotel->name_en : $selected_hotel->name_ar }}
                                                                    </th>
                                                                    <th class="text-warning" scope="col">
                                                                        {{ __('dashboard.Hotel Roles') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{ $selected_hotel->address }}
                                                                        @if(isset($selected_hotel->phone) && $selected_hotel->phone)
                                                                            <br><strong>{{ __('dashboard.hotel_phone') }}:</strong> {{ $selected_hotel->phone }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        {{ __('dashboard.Check-In') }}
                                                                        {{ $selected_hotel->check_in_time }}
                                                                        {{ __('dashboard.Check-Out') }}
                                                                        {{ $selected_hotel->check_out_time }}
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Room Details Section --}}
                                        <div class="col-md-12 mt-5">
                                            <div class="plan-section">
                                                <div class="header-summery">
                                                    <h3 class="text-dark">{{ __('dashboard.Room Details') }}</h3>
                                                    <div class="summery-options">
                                                        {{-- <a class="btn btn-warning py-2 px-5 animated fadeIn fw-normal"
                                                            href="/hotels">{{ __('dashboard.edit') }}</a> --}}
                                                    </div>
                                                </div>
                                                
                                                @php
                                                    $room_session = session('hotel.selected_room');
                                                    $selected_room = $room_session ? (is_string($room_session) ? json_decode($room_session, true) : $room_session) : null;
                                                @endphp
                                                
                                                @if($selected_room)
                                                <div class="plan-details">
                                                    <div class="plan-details-title">
                                                        <h6>{{ __('dashboard.Room Information') }}</h6>
                                                    </div>
                                                    
                                                    {{-- Room Names --}}
                                                    @if(isset($selected_room['Name']) && is_array($selected_room['Name']))
                                                        <div class="plan-details-title">
                                                            <h6><i class="fas fa-bed me-2 ms-2 text-warning"></i>{{ __('dashboard.Room Type') }}</h6>
                                                        </div>
                                                        <div class="room-type-container">
                                                            @foreach($selected_room['Name'] as $name)
                                                                <div class="room-type-card">
                                                                    <div class="room-type-icon">
                                                                        <i class="fas fa-home"></i>
                                                                    </div>
                                                                    <div class="room-type-content">
                                                                        <h6 class="room-type-title">{{ $name }}</h6>
                                                                        <span class="room-type-badge-new">Premium Room</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    {{-- Room Details Table --}}
                                                    <div class="table-responsive mb-4">
                                                        <table class="table table-bordered passenger-table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-warning" scope="col">{{ __('dashboard.Detail') }}</th>
                                                                    <th class="text-warning" scope="col">{{ __('dashboard.Information') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if(isset($selected_room['Inclusion']) && $selected_room['Inclusion'])
                                                                    <tr>
                                                                        <td><strong>{{ __('dashboard.Room Inclusions') }}</strong></td>
                                                                        <td>{{ $selected_room['Inclusion'] }}</td>
                                                                    </tr>
                                                                @endif
                                                                @if(isset($selected_room['MealType']) && $selected_room['MealType'])
                                                                    <tr>
                                                                        <td><strong>{{ __('dashboard.Meal Plan') }}</strong></td>
                                                                        <td>{{ str_replace('_', ' ', $selected_room['MealType']) }}</td>
                                                                    </tr>
                                                                @endif
                                                                <tr>
                                                                    <td><strong>{{ __('dashboard.Refund Policy') }}</strong></td>
                                                                    <td>
                                                                        @if(isset($selected_room['IsRefundable']) && $selected_room['IsRefundable'])
                                                                            <span class="status-indicator status-refundable">{{ __('dashboard.Fully Refundable') }}</span>
                                                                        @else
                                                                            <span class="status-indicator status-non-refundable">{{ __('dashboard.Non-Refundable') }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    {{-- Room Amenities --}}
                                                    @if(isset($selected_room['Amenities']) && is_array($selected_room['Amenities']) && count($selected_room['Amenities']) > 0)
                                                        <div class="plan-details-title">
                                                            <h6>{{ __('dashboard.Room Amenities') }}</h6>
                                                        </div>
                                                        <div class="room-info-card">
                                                            {{-- @php
                                                                $fixedAmenities = [
                                                                    "Non-Smoking",
                                                                    "Premium bedding",
                                                                    "Turndown service",
                                                                    "Satellite TV service",
                                                                    "Soundproofed rooms",
                                                                    "Room service",
                                                                    "In-room climate control (air conditioning)",
                                                                    "Blackout drapes/curtains",
                                                                    "Minibar",
                                                                    "Separate bathtub and shower",
                                                                    "Free wired internet",
                                                                    "Daily housekeeping",
                                                                    "Free WiFi",
                                                                    "Individually decorated",
                                                                    "Bidet",
                                                                    "Individually furnished",
                                                                    "Phone",
                                                                    "Designer toiletries",
                                                                    "MP3 docking station",
                                                                    "Child-size bathrobes",
                                                                    "Roll-in shower",
                                                                    "Connecting/adjoining rooms available",
                                                                    "Soap",
                                                                    "Mini-fridge",
                                                                    "Toilet paper",
                                                                    "Shampoo",
                                                                    "DVD player",
                                                                    "Toothbrush and toothpaste available on request",
                                                                    "Slippers",
                                                                    "Private bathroom",
                                                                    "Bathrobes",
                                                                    "Wheelchair accessible",
                                                                    "Free toiletries",
                                                                    "Hair dryer",
                                                                    "In-room safe",
                                                                    "Rainfall showerhead",
                                                                    "Rollaway/extra beds (surcharge)",
                                                                    "Room service (24 hours)",
                                                                    "Flat-panel TV",
                                                                    "Free newspaper",
                                                                    "Free bottled water"
                                                                ];
                                                            @endphp --}}
                                                            <div class="amenities-grid">
                                                                @foreach($selected_room['Amenities'] as $amenity)
                                                                    <div class="amenity-item">
                                                                        <i class="fas fa-check text-success me-2 ms-2"></i>
                                                                        {{ $amenity }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Cancellation Policy --}}
                                                    @if(isset($selected_room['CancelPolicies']) && is_array($selected_room['CancelPolicies']) && count($selected_room['CancelPolicies']) > 0)
                                                        <div class="plan-details-title">
                                                            <h6>                                                                            
                                                                <i class="fas fa-calendar-times me-2 ms-2 text-danger"></i>{{ __('dashboard.Cancellation Policy') }}</h6>
                                                        </div>
                                                        <div class="cancellation-policy-container">
                                                            @foreach($selected_room['CancelPolicies'] as $index => $policy)
                                                                <div class="cancellation-policy-card">
                                                                    <div class="policy-header">
                                                                        <div class="policy-number">{{ $index + 1 }}</div>
                                                                        <div class="policy-date">
                                                                            <i class="fas fa-calendar-alt me-2 ms-2"></i>
                                                                            <span class="date-label">{{ __('dashboard.From Date') }}</span>
                                                                            <span class="date-value">{{ \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $policy['FromDate'])->format('M d, Y') }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="policy-content">
                                                                        <div class="policy-item">
                                                                            <div class="policy-label">
                                                                                <i class="fas fa-exclamation-triangle text-danger me-2 ms-2"></i>
                                                                                {{ __('dashboard.Charge Type') }}
                                                                            </div>
                                                                            <div class="policy-value charge-type">
                                                                                @if($policy['ChargeType'] == 'Percentage')
                                                                                    {{ __('dashboard.Percentage') }}
                                                                                @elseif($policy['ChargeType'] == 'Amount')
                                                                                    {{ __('dashboard.amount') }}
                                                                                @elseif($policy['ChargeType'] == 'Fixed')
                                                                                    {{ __('dashboard.Fixed') }}
                                                                                @else
                                                                                    {{ ucfirst($policy['ChargeType']) }}
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="policy-item">
                                                                            <div class="policy-label">
                                                                                <i class="fas fa-times-circle text-danger me-2 ms-2"></i>
                                                                                {{ __('dashboard.Charge Amount') }}
                                                                            </div>
                                                                            <div class="policy-value charge-amount">
                                                                                @if($policy['ChargeType'] == 'Percentage')
                                                                                    <span class="percentage-badge">{{ $policy['CancellationCharge'] }}%</span>
                                                                                @else
                                                                                    <span class="amount-badge">{{ $policy['CancellationCharge'] }} {{ __('dashboard.SAR') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    {{-- Additional Charges/Supplements --}}
                                                    @if(isset($selected_room['Supplements']) && is_array($selected_room['Supplements']) && count($selected_room['Supplements']) > 0)
                                                        <div class="plan-details-title">
                                                            <h6>{{ __('dashboard.Additional Charges') }}</h6>
                                                        </div>
                                                        <div class="table-responsive mb-4">
                                                            <table class="table table-bordered passenger-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-warning" scope="col">{{ __('dashboard.Description') }}</th>
                                                                        <th class="text-warning" scope="col">{{ __('dashboard.Type') }}</th>
                                                                        <th class="text-warning" scope="col">{{ __('dashboard.Price') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($selected_room['Supplements'] as $supplementGroup)
                                                                        @if(is_array($supplementGroup))
                                                                            @foreach($supplementGroup as $supplement)
                                                                                <tr>
                                                                                    <td>{{ ucfirst(str_replace('_', ' ', $supplement['Description'] ?? '')) }}</td>
                                                                                    <td>{{ $supplement['Type'] ?? '' }}</td>
                                                                                    <td>{{ $supplement['Price'] ?? '' }} {{ $supplement['Currency'] ?? '' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>
                                                @else
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        {{ __('dashboard.No room details available') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-5">
                                            @foreach (session('hotel.RateConditionsHotel') as $rateCondition)
                                                {!! \App\Support\HtmlSanitizer::sanitizeLimitedHtml(html_entity_decode($rateCondition)) !!}
                                                <hr>
                                            @endforeach
                                        </div>
                                        </div> <!-- End hotel-section -->
                                    @endif

                                    @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                        <div class="col-md-12 mt-5 mb-5">
                                            <div class="plan-section">
                                                <div class="header-summery">
                                                    <h3 class="text-dark">{{ __('dashboard.Flight Details') }}</h3>
                                                    <div class="summery-options">
                                                        <a class="btn btn-warning py-2 px-5 animated fadeIn fw-normal"
                                                            href="/flights">{{ __('dashboard.edit') }}</a>
                                                    </div>
                                                </div>
                                                @php
                                                $inbound_flightSegment=session()->has('flight.inbound_flightSegment')?session('flight.inbound_flightSegment'):null;
                                                
                                                // Extract the actual flight segment data from the nested structure
                                                $flightSegmentData = session('flight.flightSegment');
                                                
                                                // Based on the debug output: flightSegment[0][0] contains the actual flight data
                                                // We need to restructure it for the component
                                                $extractedFlightSegment = [];
                                                
                                                if ($flightSegmentData && is_array($flightSegmentData)) {
                                                    // The component expects: $flightSegment[0] = array of segments
                                                    // But our data has: $flightSegmentData[0][0] = single segment
                                                    // So we need to restructure it properly
                                                    foreach ($flightSegmentData as $index => $flightGroup) {
                                                        if (is_array($flightGroup)) {
                                                            // Pass the flightGroup (which contains the segments) directly
                                                            $extractedFlightSegment[$index] = $flightGroup;
                                                        }
                                                    }
                                                } else {
                                                    $extractedFlightSegment = [];
                                                }
                                                @endphp
                                                <x-flight-details  :flightSegment="$extractedFlightSegment" :inboundFlightSegment="$inbound_flightSegment"></x-flight-details>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="summary-calculate">
                                    <h3 class="summary-title">{{ __('dashboard.Payment Details') }}</h3>
                                    <ul class="summary-list">
                                        @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                            <li>
                                                <span class="price-title">
                                                    {{ __('dashboard.Flight') }} :
                                                </span>
                                                <span
                                                    class="price-mount">{{ session('flight.flight_amount') + (session()->has('flight.inbound_flightSegment') ? session('flight.inbound_flight_amount') : 0) }}
                                                    {{ __('dashboard.SAR') }}</span>
                                            </li>
                                             @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [3]))

                                                <li class="total-price">
                                                    <span class="price-title text-primary">{{ __('trans.taxes') }} :</span>

                                                    <span
                                                        class="price-mount text-primary">{{ $totalPrice - (session('flight.flight_amount') + (session()->has('flight.inbound_flightSegment') ? session('flight.inbound_flight_amount') : 0)) }}
                                                        {{ $currency }}</span>
                                                </li>
                                            @endif
                                        @endif

                                        @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 2]))
                                            <li>
                                                <span class="price-title">
                                                    {{ __('dashboard.Hotel') }} :
                                                </span>
                                                <span class="price-mount">{{ session('hotel.TotalFareHotel') }}
                                                    {{ __('dashboard.SAR') }}</span>
                                            </li>
                                            @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [2]))

                                                 
                                                <li class="total-price">
                                                    <span class="price-title text-primary">{{ __('trans.taxes') }} :</span>

                                                    <span
                                                        class="price-mount text-primary">{{ $totalPrice - session('hotel.TotalFareHotel') }}
                                                        {{ $currency }}</span>
                                                </li>
                                            @endif

                                        @endif

                                                @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1]))

                                                 
                                                <li class="total-price">
                                                    <span class="price-title text-primary">{{ __('trans.taxes') }} :</span>

                                                    <span
                                                        class="price-mount text-primary">{{ $totalPrice - session('hotel.TotalFareHotel') - (session('flight.flight_amount') + (session()->has('flight.inbound_flightSegment') ? session('flight.inbound_flight_amount') : 0)) }}
                                                        {{ $currency }}</span>
                                                </li>
                                            @endif

                                        <!-- SSR (Special Services) Cost -->
                                        @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))
                                            <li class="ssr-cost-item" style="display: none;">
                                                <span class="price-title">
                                                    {{ __('dashboard.Special Services') }} :
                                                </span>
                                                <span class="price-mount">
                                                    <span id="payment_ssr_cost">0</span> {{ __('dashboard.SAR') }}
                                                </span>
                                            </li>
                                        @endif

                                        <li class="total-price">
                                            <span class="price-title">{{ __('dashboard.Total') }} :</span>

                                            @if (in_array($tripType, [1, 2, 3]))
                                                <span class="price-mount">
                                                    <span id="final_total_price">{{ $totalPrice }}</span>
                                                    {{ $currency }}
                                                </span>
                                            @endif
                                        </li>
                                        @if (isset(session('preferences')['trip_type']) && in_array(session('preferences')['trip_type'], [1, 3]))

                                            <li class="justify-content-center">
                                                <span class="price-mount text-danger">
                                                    @php
                                                        $outboundRefundable = session('outbound_flight');
                                                        $inboundRefundable = session('inbound_flight');
                                                    @endphp

                                                    @if ($outboundRefundable)
                                                        {{ $outboundRefundable['IsRefundable'] == false ? __('dashboard.NonRefundable') : __('dashboard.Refundable') }}
                                                    @endif
                                                    @if ($inboundRefundable)
                                                        <br>
                                                        {{ $outboundRefundable['IsRefundable'] == false ? __('dashboard.NonRefundable') : __('dashboard.Refundable') }}
                                                    @endif
                                                    <span>
                                            </li>
                                        @endif
                                    </ul>

                                    @if (session('passengers'))
                                        <button type="submit" class="btn btn-warning py-3 w-100 mt-4 animated fadeIn">
                                            {{ __('dashboard.Proceed payment') }}
                                        </button>
                                    @else
                                        <p class="alert alert-danger">{{ __('dashboard.Please add passengers') }}</p>
                                    @endif


                                    {{-- <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                        <label class="form-check-label" for="exampleCheck1">You agree to our <a
                                                href="#">Privacy
                                                Policy</a></label>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                    </div>
            </div>
            </form>
        </div>
    </div>

    <div id="expired_modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <p style="font-weight: 900;margin: 0px;">
                    {{ __('messages.Please refresh your search for the latest prices') }}</p>
            </div>
            <p>{{ __('messages.Flight prices change frequently due to availability and demand. We want to make sure you always see the most recent prices available') }}
            </p>
            <button id="redirectBtn" class="btn">{{ __('messages.refresh') }}</button>
        </div>
    </div>

    <!-- Modal -->
    @include('website.modals.passenger')
    <!-- End Modal -->

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

        /* Modal styling */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1;
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

        #expired_modal .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        #expired_modal .btn:hover {
            background-color: #45a049;
        }

        /* Table styling */
        .passenger-table tr th {
            background-color: #FBFBFB;
            padding: 1em;
            font-weight: 500;
            border: 1px solid #dee2e6;
        }

        .passenger-table tr td {
            padding: 0.75em 1em;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .passenger-table {
            margin-bottom: 0;
        }

        .passenger-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .passenger-table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* SSR (Special Service Request) Styles */
        .ssr-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        .passenger-ssr {
            background-color: white;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            transition: all 0.3s ease;
        }

        .passenger-ssr:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-color: #007bff !important;
        }

        .meal-options .form-check, 
        .baggage-options .form-check {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 8px !important;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }

        .meal-options .form-check:hover, 
        .baggage-options .form-check:hover {
            background-color: #e9ecef;
            border-color: #007bff;
        }

        .meal-options .form-check-input:checked + .form-check-label,
        .baggage-options .form-check-input:checked + .form-check-label {
            font-weight: 600;
            color: #007bff;
        }

        .meal-options .form-check:has(.form-check-input:checked),
        .baggage-options .form-check:has(.form-check-input:checked) {
            background-color: #e7f3ff;
            border-color: #007bff;
        }

        .passenger-ssr-summary {
            border-left: 4px solid #17a2b8;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }

        .ssr-total-summary {
            border: 2px solid #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }

        .ssr-cost-item {
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            margin-top: 10px;
        }

        .ssr-cost-item .price-title {
            color: #17a2b8;
            font-weight: 500;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .passenger-ssr .row {
                margin: 0;
            }
            
            .passenger-ssr .col-md-6 {
                padding: 10px 0;
            }
            
            .ssr-total-summary .row {
                text-align: center !important;
            }
            
            .ssr-total-summary .col-md-4 {
                text-align: center !important;
                margin-top: 10px;
            }
        }

        /* Fix modal z-index to appear above navbar */
        /* #staticBackdrop {
            z-index: 8890 !important;
        }
        
        #staticBackdrop .modal-dialog {
            z-index: 8891 !important;
        }
        
        .modal-backdrop {
            z-index: 8889 !important;
        }
        .select2-dropdown--below{
            z-index: 9999 !important;
        } */
        
        /* Field validation error styles */
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .field-error {
            font-size: 0.875rem;
            margin-top: 0.25rem !important;
        }
        
        /* Name validation error styles for passenger modal */
        .name-error, .duplicate-error, .age-error {
            color: #dc3545 !important;
            font-size: 0.75rem !important;
            font-weight: 500;
            margin-top: 0.25rem;
            display: block;
        }
        
        /* Highlight invalid name fields */
        input[name="first_name[]"]:invalid,
        input[name="last_name[]"]:invalid {
            border-color: #dc3545 !important;
            background-color: #fff5f5 !important;
        }

        /* ===== HOTEL SECTION STYLES ===== */
        /* Scope all hotel-related styles to avoid affecting flight sections */
        .hotel-section {
            /* Hotel-specific container styles */
        }

        /* RTL Improvements for Room Details Section - HOTEL ONLY */
        .hotel-section .plan-details-title h6 {
            margin-bottom: 1rem;
            font-weight: 600;
            color: #333;
        }

        /* RTL Table Improvements - HOTEL ONLY */
        [dir="rtl"] .hotel-section .passenger-table th,
        [dir="rtl"] .hotel-section .passenger-table td {
            text-align: right;
            padding: 1rem 1.5rem;
        }

        [dir="rtl"] .hotel-section .passenger-table th:first-child,
        [dir="rtl"] .hotel-section .passenger-table td:first-child {
            border-right: 1px solid #dee2e6;
        }

        [dir="rtl"] .hotel-section .passenger-table th:last-child,
        [dir="rtl"] .hotel-section .passenger-table td:last-child {
            border-left: 1px solid #dee2e6;
        }

        /* Room Details Specific RTL Styling - HOTEL ONLY */
        [dir="rtl"] .hotel-section .plan-details .table-responsive {
            margin-bottom: 2rem;
        }

        [dir="rtl"] .hotel-section .plan-details-title {
            margin-bottom: 1.5rem;
            text-align: right;
        }

        [dir="rtl"] .hotel-section .plan-details-title h6 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            border-right: 3px solid #ffc107;
            padding-right: 1rem;
            margin-bottom: 1rem;
        }

        /* Improve spacing for room information cards - HOTEL ONLY */
        .hotel-section .room-info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        [dir="rtl"] .hotel-section .room-info-card {
            text-align: right;
        }

        /* Room amenities list styling - HOTEL ONLY */
        .hotel-section .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        [dir="rtl"] .hotel-section .amenities-grid {
            text-align: right;
        }

        .hotel-section .amenity-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .hotel-section .amenity-item:hover {
            background: #f8f9fa;
            border-color: #ffc107;
        }

        [dir="rtl"] .hotel-section .amenity-item {
            direction: rtl;
            text-align: right;
        }

        [dir="rtl"] .hotel-section .amenity-item i {
            margin-right: 0;
            margin-left: 0.5rem;
        }

        /* Room type badges - HOTEL ONLY */
        .hotel-section .room-type-badge {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            color: #fff;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-weight: 600;
            display: inline-block;
            margin: 0.3rem;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
            transition: all 0.3s ease;
        }

        .hotel-section .room-type-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.4);
        }

        [dir="rtl"] .hotel-section .room-type-badge {
            margin: 0.3rem;
        }

        /* Room info card improvements - HOTEL ONLY */
        .hotel-section .room-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        [dir="rtl"] .hotel-section .room-info-card {
            text-align: right;
        }

        /* Status indicators - HOTEL ONLY */
        .hotel-section .status-indicator {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .hotel-section .status-refundable {
            background-color: #d4edda;
            color: #155724;
        }

        .hotel-section .status-non-refundable {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* New Room Type Card Styling - HOTEL ONLY */
        .hotel-section .room-type-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .hotel-section .room-type-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.4s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }

        .hotel-section .room-type-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ffc107, #ffb300);
        }

        .hotel-section .room-type-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
            border-color: #ffc107;
        }

        .hotel-section .room-type-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffc107, #ffb300);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        .hotel-section .room-type-icon i {
            font-size: 1.5rem;
            color: #fff;
        }

        .hotel-section .room-type-content {
            flex: 1;
        }

        .hotel-section .room-type-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .hotel-section .room-type-badge-new {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: #fff;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
        }

        /* RTL Support for Room Type Cards - HOTEL ONLY */
        [dir="rtl"] .hotel-section .room-type-card {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .hotel-section .room-type-content {
            text-align: right;
        }

        /* Cancellation Policy Card Styling - HOTEL ONLY */
        .hotel-section .cancellation-policy-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .hotel-section .cancellation-policy-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .hotel-section .cancellation-policy-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: #dc3545;
        }

        .hotel-section .policy-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: #fff;
        }

        .hotel-section .policy-number {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
        }

        .hotel-section .policy-date {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .hotel-section .date-label {
            font-weight: 600;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .hotel-section .date-value {
            font-weight: 700;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .hotel-section .policy-content {
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .hotel-section .policy-item {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .hotel-section .policy-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #495057;
            font-size: 0.95rem;
        }

        .hotel-section .policy-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .hotel-section .charge-type {
            color: #dc3545;
            background: #f8d7da;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            text-transform: capitalize;
        }

        .hotel-section .percentage-badge {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 700;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .hotel-section .amount-badge {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 700;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        /* RTL Support for Cancellation Policy - HOTEL ONLY */
        [dir="rtl"] .hotel-section .policy-header {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .hotel-section .policy-date {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .hotel-section .policy-item {
            text-align: right;
        }

        [dir="rtl"] .hotel-section .policy-label {
            flex-direction: row-reverse;
        }

        /* Policy table improvements - HOTEL ONLY */
        .hotel-section .policy-table td strong {
            font-weight: 600;
            color: #495057;
        }

        [dir="rtl"] .hotel-section .policy-table td {
            text-align: right;
        }

        /* Responsive Design for amenities - HOTEL ONLY */
        @media (max-width: 768px) {
            .hotel-section .amenities-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .hotel-section .amenity-item {
                font-size: 0.85rem;
                padding: 0.4rem;
            }

            .hotel-section .room-type-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .hotel-section .room-type-card {
                padding: 1.5rem;
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .hotel-section .room-type-icon {
                width: 50px;
                height: 50px;
            }

            .hotel-section .room-type-icon i {
                font-size: 1.25rem;
            }

            .hotel-section .room-type-title {
                font-size: 1.1rem;
            }

            .hotel-section .policy-header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .hotel-section .policy-date {
                justify-content: center;
            }

            .hotel-section .policy-content {
                padding: 1.5rem;
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            [dir="rtl"] .hotel-section .room-type-card {
                flex-direction: column;
            }

            [dir="rtl"] .hotel-section .policy-header {
                flex-direction: column;
            }
        }

        /* ===== FLIGHT SECTION STYLES ===== */
        /* Keep original flight styles unchanged */
        .airway-details {
            display: flex;
            align-items: center;
            justify-content: stretch;
            gap: 20px;
            justify-content: space-between;
            margin-bottom: 2em;
            flex-wrap: nowrap;
        }
    
    </style>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // Check if passenger form was submitted successfully and close modal
            @if(session('passenger_success'))
                console.log('Passenger form submitted successfully, closing modal...');
                
                // Show success message briefly before closing modal
                setTimeout(function() {
                    // Hide the modal
                    $('#staticBackdrop').modal('hide');
                    
                    // Optional: Show a brief success notification
                    if (typeof showSuccessMessage === 'function') {
                        showSuccessMessage('Passengers saved successfully!');
                    }
                }, 100);
                
                // Show success message (optional)
                @if(session('passengers'))
                    console.log('Passengers saved successfully:', @json(count(session('passengers'))), 'passengers');
                @endif
            @endif
            
            // Check if there are validation errors and automatically open modal
            @if($errors->any())
                console.log('Validation errors found:', @json($errors->all()));
                
                // First scroll to middle of page smoothly
                $('html, body').animate({
                    scrollTop: $(document).height() / 2
                }, 800, function() {
                    // After scrolling is complete, show the modal
                    setTimeout(function() {
                        $('#staticBackdrop').modal('show');
                    }, 300); // Small additional delay after scroll completion
                });
                
                // Wait for modal to be shown, then expand all collapsed sections
                $('#staticBackdrop').on('shown.bs.modal', function () {
                    console.log('Modal shown due to validation errors, expanding all passenger sections...');
                    
                    // Expand all adult passenger sections
                    $('.collapse[id^="collapseExampleAdults"]').each(function() {
                        $(this).addClass('show');
                        console.log('Expanded adult section:', $(this).attr('id'));
                    });
                    $('a[href^="#collapseExampleAdults"]').removeClass('collapsed').attr('aria-expanded', 'true');
                    
                    // Expand all children passenger sections
                    $('.collapse[id^="collapseExampleChildren"]').each(function() {
                        $(this).addClass('show');
                        console.log('Expanded children section:', $(this).attr('id'));
                    });
                    $('a[href^="#collapseExampleChildren"]').removeClass('collapsed').attr('aria-expanded', 'true');
                    
                    // Expand all infant passenger sections
                    $('.collapse[id^="collapseExampleInfants"]').each(function() {
                        $(this).addClass('show');
                        console.log('Expanded infant section:', $(this).attr('id'));
                    });
                    $('a[href^="#collapseExampleInfants"]').removeClass('collapsed').attr('aria-expanded', 'true');
                    
                    console.log('All passenger sections have been expanded successfully due to validation errors');
                    
                    // Scroll to top of modal content
                    $('.modal-body').scrollTop(0);
                });
                
                // Also trigger form data restoration after modal opens
                $('#staticBackdrop').on('shown.bs.modal', function () {
                    // Trigger form data restoration if the function exists
                    if (typeof restoreFormData === 'function') {
                        setTimeout(function() {
                            restoreFormData();
                            console.log('Form data restoration triggered due to validation errors');
                        }, 100);
                    }
                    
                    // Display Laravel validation errors under each field
                    @if($errors->any())
                        displayFieldErrors(@json($errors->toArray()));
                    @endif
                });
            @else
                // Only log when no validation errors - modal will not auto-open
                console.log('No validation errors found - passenger modal will only open manually');
            @endif

            var expiry_flight_time_check =
                '{{ isset(session('flight')['flight_expired_time']) ? session('flight')['flight_expired_time'] : 0 }}';

            if (expiry_flight_time_check) {
                var expiry_flight_time = new Date(expiry_flight_time_check);
                setInterval(function() {
                    var current_time = new Date();
                    var time_difference = expiry_flight_time - current_time;
                    var minutes = Math.floor(time_difference / 1000 / 60);
                    var seconds = Math.floor(time_difference / 1000) % 60;

                    if (expiry_flight_time_check != 0 && time_difference < 0) {
                        $('#expired_modal').show();
                        return;
                    }
                }, 60000);

                var current_time = new Date();
                var time_difference = expiry_flight_time - current_time;
                var minutes = Math.floor(time_difference / 1000 / 60);
                var seconds = Math.floor(time_difference / 1000) % 60;

                if (expiry_flight_time_check != 0 && time_difference < 0) {
                    $('#expired_modal').show();
                    return;
                }

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
        
        // Function to display Laravel validation errors under input fields
        function displayFieldErrors(errors) {
            console.log('Displaying field errors:', errors);
            
            // Clear any existing error messages first
            $('.field-error').remove();
            
            // Loop through each error field
            Object.keys(errors).forEach(function(fieldName) {
                const errorMessages = errors[fieldName];
                
                // Handle array fields (like first_name.0, last_name.1, etc.)
                if (fieldName.includes('.')) {
                    const parts = fieldName.split('.');
                    const baseFieldName = parts[0];
                    const index = parseInt(parts[1]);
                    
                    // Find the corresponding input field
                    const fieldSelector = `input[name="${baseFieldName}[]"]:eq(${index}), select[name="${baseFieldName}[]"]:eq(${index})`;
                    const field = $(fieldSelector);
                    
                    if (field.length > 0) {
                        // Add error class to field
                        field.addClass('is-invalid');
                        
                        // Create error message element
                        const errorDiv = $('<div class="field-error text-danger small mt-1 text-start"></div>');
                        errorDiv.text(errorMessages[0]); // Show first error message
                        
                        // Insert error message after the field
                        field.parent().append(errorDiv);
                        
                        console.log(`Added error for ${fieldName}:`, errorMessages[0]);
                    }
                } else {
                    // Handle non-array fields
                    const field = $(`input[name="${fieldName}"], select[name="${fieldName}"]`);
                    
                    if (field.length > 0) {
                        // Add error class to field
                        field.addClass('is-invalid');
                        
                        // Create error message element
                        const errorDiv = $('<div class="field-error text-danger small mt-1 text-start"></div>');
                        errorDiv.text(errorMessages[0]); // Show first error message
                        
                        // Insert error message after the field
                        field.parent().append(errorDiv);
                        
                        console.log(`Added error for ${fieldName}:`, errorMessages[0]);
                    }
                }
            });
        }
    </script>

    <!-- SSR (Special Service Request) JavaScript -->
    <script>
        $(document).ready(function() {
            let originalTotalPrice = {{ $totalPrice }};
            let totalSSRCost = 0;
            let passengerSSRData = {};

            // Initialize passenger SSR data
            @foreach ($passengers as $index => $passenger)
                passengerSSRData[{{ $index }}] = {
                    meal: { type: 'none', price: 0 },
                    baggage: { type: 'none', price: 0 },
                    total: 0
                };
            @endforeach

            // Handle meal selection
            $('.meal-radio').on('change', function() {
                const passengerIndex = $(this).data('passenger');
                const mealType = $(this).val();
                const mealPrice = parseFloat($(this).data('price'));

                // Update passenger SSR data
                passengerSSRData[passengerIndex].meal = {
                    type: mealType,
                    price: mealPrice
                };

                // Update session data via AJAX
                updatePassengerSessionSSR(passengerIndex, 'meal', mealType, mealPrice);
                
                updatePassengerSSRSummary(passengerIndex);
                updateTotalSSRCost();
            });

            // Handle baggage selection
            $('.baggage-radio').on('change', function() {
                const passengerIndex = $(this).data('passenger');
                const baggageType = $(this).val();
                const baggagePrice = parseFloat($(this).data('price'));

                // Update passenger SSR data
                passengerSSRData[passengerIndex].baggage = {
                    type: baggageType,
                    price: baggagePrice
                };

                // Update session data via AJAX
                updatePassengerSessionSSR(passengerIndex, 'baggage', baggageType, baggagePrice);

                updatePassengerSSRSummary(passengerIndex);
                updateTotalSSRCost();
            });

            /**
             * Update passenger SSR data in session via AJAX
             */
            function updatePassengerSessionSSR(passengerIndex, ssrType, ssrValue, ssrPrice) {
                $.ajax({
                    url: '{{ route("passenger.update-ssr") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        passenger_index: passengerIndex,
                        ssr_type: ssrType,
                        ssr_value: ssrValue,
                        ssr_price: ssrPrice
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log(`Updated ${ssrType} for passenger ${passengerIndex}:`, response.passenger);
                            
                            // Call debug function to show updated session data (only in development)
                            if (window.location.hostname === 'localhost' || window.location.hostname.includes('127.0.0.1')) {
                                setTimeout(checkPassengerSessionData, 100);
                            }
                        } else {
                            console.error('Failed to update SSR data:', response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error updating SSR data:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                    }
                });
            }

            function updatePassengerSSRSummary(passengerIndex) {
                const passenger = passengerSSRData[passengerIndex];
                const summaryDiv = $(`#ssr_summary_${passengerIndex}`);
                const servicesSpan = summaryDiv.find('.selected-services');
                const costSpan = summaryDiv.find('.additional-cost');

                let services = [];
                let totalCost = 0;

                // Add meal service if selected
                if (passenger.meal.type !== 'none') {
                    const mealLabels = {
                        'VGML': '{{ __('dashboard.Vegetarian Meal') }}',
                        'MOML': '{{ __('dashboard.Halal Meal') }}',
                        'KSML': '{{ __('dashboard.Kosher Meal') }}',
                        'CHML': '{{ __('dashboard.Child Meal') }}'
                    };
                    services.push(`${mealLabels[passenger.meal.type]} (${passenger.meal.price} SAR)`);
                    totalCost += passenger.meal.price;
                }

                // Add baggage service if selected
                if (passenger.baggage.type !== 'none') {
                    const baggageLabels = {
                        '5KG': '{{ __('dashboard.Extra 5kg') }}',
                        '10KG': '{{ __('dashboard.Extra 10kg') }}',
                        '15KG': '{{ __('dashboard.Extra 15kg') }}',
                        '20KG': '{{ __('dashboard.Extra 20kg') }}'
                    };
                    services.push(`${baggageLabels[passenger.baggage.type]} (${passenger.baggage.price} SAR)`);
                    totalCost += passenger.baggage.price;
                }

                // Update passenger total
                passenger.total = totalCost;

                // Show/hide summary based on whether services are selected
                if (services.length > 0) {
                    servicesSpan.text(services.join(', '));
                    costSpan.text(`${totalCost} SAR`);
                    summaryDiv.show();
                } else {
                    summaryDiv.hide();
                }
            }

            function updateTotalSSRCost() {
                // Calculate total SSR cost across all passengers
                totalSSRCost = 0;
                Object.values(passengerSSRData).forEach(passenger => {
                    totalSSRCost += passenger.total;
                });

                // Update SSR cost display
                $('#total_ssr_cost').text(totalSSRCost);
                $('#payment_ssr_cost').text(totalSSRCost);

                // Show/hide SSR cost line item in payment summary
                if (totalSSRCost > 0) {
                    $('.ssr-cost-item').show();
                } else {
                    $('.ssr-cost-item').hide();
                }

                // Update final total price
                const finalTotal = originalTotalPrice + totalSSRCost;
                $('#final_total_price').text(finalTotal.toFixed(2));

                console.log('Updated SSR costs:', passengerSSRData);
                console.log('Total SSR cost:', totalSSRCost);
                console.log('Final total:', finalTotal.toFixed(2));
            }

            /**
             * Debug function to check current passenger session data
             */
            function checkPassengerSessionData() {
                $.ajax({
                    url: '/test-passengers',
                    method: 'GET',
                    success: function(response) {
                        console.log('Current passenger session data:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching passenger session data:', error);
                    }
                });
            }

            // Add a button to check passenger data (for debugging - can be removed later)
            if (window.location.hostname === 'localhost' || window.location.hostname.includes('127.0.0.1')) {
                console.log('Debug mode: You can call checkPassengerSessionData() to view current session data');
                window.checkPassengerSessionData = checkPassengerSessionData;
            }

            // Add SSR data to form submission
            $('form.steps-form').on('submit', function(e) {
                console.log('Form submission - SSR data being sent:', passengerSSRData);
                
                // Add hidden inputs for SSR data
                Object.keys(passengerSSRData).forEach(passengerIndex => {
                    const passenger = passengerSSRData[passengerIndex];
                    
                    // Add meal selection
                    if (passenger.meal.type !== 'none') {
                        console.log(`Adding meal for passenger ${passengerIndex}:`, passenger.meal);
                        $(this).append(`<input type="hidden" name="ssr[${passengerIndex}][meal][type]" value="${passenger.meal.type}">`);
                        $(this).append(`<input type="hidden" name="ssr[${passengerIndex}][meal][price]" value="${passenger.meal.price}">`);
                    }
                    
                    // Add baggage selection
                    if (passenger.baggage.type !== 'none') {
                        console.log(`Adding baggage for passenger ${passengerIndex}:`, passenger.baggage);
                        $(this).append(`<input type="hidden" name="ssr[${passengerIndex}][baggage][type]" value="${passenger.baggage.type}">`);
                        $(this).append(`<input type="hidden" name="ssr[${passengerIndex}][baggage][price]" value="${passenger.baggage.price}">`);
                    }
                });

                // Add total SSR cost
                $(this).append(`<input type="hidden" name="total_ssr_cost" value="${totalSSRCost}">`);
                $(this).append(`<input type="hidden" name="final_total_price" value="${(originalTotalPrice + totalSSRCost).toFixed(2)}">`);

                console.log('Form submitted with SSR data - Total SSR cost:', totalSSRCost);
            });
        });
    </script>
@endsection
