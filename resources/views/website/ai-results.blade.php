@extends('website.layout')
@section('title', __('dashboard.AI Trip Planner'))
@section('content')
    <div style="background-image: url('site/img/ai.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

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

    @php
        $hotels = json_decode($hotels);
        $hotels = (new Illuminate\Pagination\LengthAwarePaginator(
            items: is_array($hotels) ? $hotels : $hotels->data,
            total: is_array($hotels) ? count($hotels) : $hotels->total,
            perPage: is_array($hotels) ? 1 : $hotels->per_page,
            currentPage: is_array($hotels) ? 1 : $hotels->current_page,
            options: [],
        ))->withPath('/ai-results');
    @endphp

    <!-- steps form -->
    <div class="container hotel-single">
        <div class="row">
            <div class="col-md-12">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="plan-section mt-4">
                                @if (isset($city_data['photos']) && count($city_data['photos']) > 0)
                                    <div class="plan-gallery">
                                        <div class="container p-0">
                                            <div class="row justify-content-center">
                                                <div class="col col-md-12 gallery-container-wrap position-relative">
                                                    <div class="gallery-container" id="gallery-dynamic-thumbnails">
                                                        @foreach ($city_data['photos'] as $key => $photo)
                                                            <a class="gallery-item" data-index="{{ $key }}"
                                                                data-src="{{ $photo }}">
                                                                <img alt="layers of blue." class="img-responsive"
                                                                    src="{{ $photo }}" />
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="plan-actions">
                                    <div class="plan-title">
                                        @if (app()->getLocale() == 'ar')
                                            <h1>رحلتك الى
                                                {{ isset($city_data['city_name']) ? $city_data['city_name'] : '' }} لمدة
                                                {{ isset($city_data['days_count']) ? $city_data['days_count'] : '' }} يوم
                                            </h1>
                                        @else
                                            <h1>Your Trip to
                                                {{ isset($city_data['city_name']) ? $city_data['city_name'] : '' }} For
                                                {{ isset($city_data['days_count']) ? $city_data['days_count'] : '' }} Days
                                            </h1>
                                        @endif
                                        {{-- <a class="save">
                                            <i class="bi bi-bookmark"></i>
                                        </a>
                                        <a href="#" class="share">
                                            <i class="bi bi-download"></i>
                                        </a> --}}
                                    </div>
                                </div>
                                <hr>

                                <div class="ai-content">
                                    <div class="title text-dark fw-bold mb-2">
                                        {{ __('dashboard.Overview') }}
                                    </div>
                                    <p>
                                        {{ isset($city_data['city_desc']) ? $city_data['city_desc'] : '' }}
                                    </p>

                                    <div class="map-iframe">
                                        <div id="map" style="height: 500px; width: 100%;"></div>
                                        {{-- <iframe
                                        src="https://maps.google.com/maps?q={{ isset($city_data['lat']) ? $city_data['lat'] : '' }},{{ isset($city_data['lng']) ? $city_data['lng'] : '' }}&hl=es&z=14&amp;output=embed"
                                        width="100%" height="200" frameborder="0" style="border:0">
                                    </iframe> --}}
                                    </div>
                                </div>

                                <div class="ai-stay-places">
                                    <div class="title text-dark fw-bold mb-2">{{ __('dashboard.Places_stay') }}</div>
                                    <hr>
                                    <p class="subtitle">
                                        {{ __('dashboard.recommended_stay') }}
                                    </p>

                                    <div style="height: auto;overflow:hidden" class="hotels-cards">
                                        <div class="owl-carousel hotels-carousel p-0">
                                            @foreach ($hotels as $hotel)
                                                <div class="owl-carousel-item">
                                                    <div style="direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
                                                        class="card">
                                                        <div class="owl-carousel hotel-image-carousel">
                                                            @foreach (collect(@$hotel->images)->take(5)->toArray() as $image)
                                                                <div class="owl-carousel-item">
                                                                    <div class="img-container">
                                                                        <img src="{{ $image }}" />
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <div class="card-content">
                                                            <a href="/hotel/{{ @$hotel->code }}" target="_blank"
                                                                class="hotel-name">
                                                                {{-- <a class="hotel-name"> --}}
                                                                <h2>{{ app()->getLocale() == 'ar' ? ($hotel->name_ar ?: $hotel->name_en) : ($hotel->name_en ?: $hotel->name_ar) }}
                                                                </h2>
                                                            </a>
                                                            <ul class="hotel-list-options">
                                                                @php
                                                                    $facilities =
                                                                        app()->getLocale() == 'ar'
                                                                            ? ($hotel->facilities_ar ?:
                                                                            $hotel->facilities_en)
                                                                            : ($hotel->facilities_en ?:
                                                                            $hotel->facilities_ar);
                                                                    $facilities = collect($facilities)
                                                                        ->take(5)
                                                                        ->toArray();
                                                                @endphp

                                                                @foreach ($facilities as $facility)
                                                                    <li
                                                                        style="font-size: 12px;background: aquamarine;border-radius: 15px;box-shadow: aquamarine 2px 2px;">
                                                                        {{ $facility }}</li>
                                                                @endforeach
                                                            </ul>

                                                            <?php
                                                            $text = Str::limit(app()->getLocale() == 'ar' ? ($hotel->description_ar ?: $hotel->description_en) : ($hotel->description_en ?: $hotel->description_ar), 200, '...');
                                                            $direction = preg_match('/\p{Arabic}/u', $text) ? 'rtl' : 'ltr';
                                                            ?>
                                                            <hr>
                                                            <div style="direction:{{ $direction }}" class="excerpt">
                                                                {!! \App\Support\HtmlSanitizer::sanitizeLimitedHtml($text) !!}
                                                            </div>
                                                            <hr>
                                                            <div class="collection-sections">
                                                                <div class="section-first">
                                                                    <span class="text-success offer-refund">
                                                                        {{ @$hotel->rooms->is_refundable ? __('Refundable') : __('Not Refundable') }}
                                                                    </span>
                                                                    <div class="hotel-rate">
                                                                        <div class="hotel-stars">
                                                                            <input class="rating rating-loading"
                                                                                data-min="0" data-max="5" data-step="1"
                                                                                value="{{ @$hotel->new_rating }}"
                                                                                data-size="xs" disabled="" hidden>
                                                                        </div>
                                                                        {{-- <span class="rate-box">
                                                                {{ @$hotel->rating }}</span> --}}
                                                                        <span
                                                                            class="hotel-price">{{ @$hotel->rooms->Price }}
                                                                            {{ __('dashboard.SAR') }}</span>
                                                                    </div>
                                                                    <div class="hotel-location">
                                                                        <span
                                                                            class="location-title">{{ __('dashboard.distance_from_center_of_city') }}
                                                                            : {{ round((@$hotel->distance / 1000), 2) }}
                                                                            {{ __('dashboard.kilometer') }} </span>
                                                                        <span
                                                                            class="location-title">{{ __('dashboard.address') }}
                                                                            :
                                                                        </span>
                                                                        <span
                                                                            class="location-desc">{{ @$hotel->address }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="section-second">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="ai-visit-places" id="visit-places">
                                    <div class="title text-dark fw-bold mb-2">{{ __('dashboard.Places To Visit') }}</div>
                                    <hr>
                                    <p class="subtitle">
                                        {{-- Welcome to Jeddah, Saudi Arabia! --}}
                                    </p>
                                    <div id="loading-spinner" style="display:none;">
                                        <div class="spinner"></div>
                                    </div>
                                    <div style="display: none" class="row all-row">
                                        <div class="col-md-12">
                                            <div class="days">

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
    </div>
    <!-- Steps form End -->
@endsection
@section('newCss')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #loading-spinner {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .collapse-div p:has(> a) span {
            display: inline-block
        }

        .hotels-cards .card-content {
            display: flex;
            flex-direction: column;
        }

        .hotels-cards .card {
            height: auto;
            margin-bottom: 1em
        }

        .hotels-cards .card .owl-carousel.owl-drag .owl-item,
        .hotels-cards .card .owl-carousel .owl-stage,
        .hotels-cards .card .owl-carousel .owl-stage-outer,
        .hotels-cards .card .owl-carousel-item,
        .hotels-cards .card .owl-carousel-item img {
            max-height: 100%
        }
    </style>
@endsection
@section('newJs')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async
        defer></script>
    <script>
        var map;
        var markers = [];

        function initMap(lat = 40.730610, lng = -73.935242) {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 11,
                center: {
                    lat: lat,
                    lng: lng
                }
            });
        }

        function addMarker(lat, lng, title) {
            var marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: map,
                title: title
            });
            markers.push(marker);
        }

        $(document).ready(function() {
            $('#loading-spinner').show();
            $.ajax({
                url: '/ai-trip-planner',
                type: 'POST',
                data: {
                    startDate: "{{ $datastart_date }}",
                    endDate: "{{ $end_date }}",
                    destination: "{{ $city }}",
                    interests: {!! json_encode($travelInterests) !!},
                    come_with: "{{ $groupType }}",
                    food: {!! json_encode($food) !!}
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loading-spinner').hide();
                    $('.all-row').show();
                    if (response && response.length > 0 && response[0].itinerary) {
                        const itinerary = response[0].itinerary;
                        $('#visit-places .days').empty();

                        // Get the first activity's location from the itinerary
                        const firstActivity = itinerary[0].morning[0] || itinerary[0].afternoon[0] ||
                            itinerary[0].evening[0];
                        const firstActivityLat = firstActivity ? firstActivity.lat : 40.730610;
                        const firstActivityLng = firstActivity ? firstActivity.lng : -73.935242;

                        // Initialize the map with the first activity's location
                        initMap(firstActivityLat, firstActivityLng);

                        itinerary.forEach((day, index) => {
                            const weatherForecast = day.weather?.forecast || day.weather
                                ?.description;
                            const weatherTemperature = day.weather?.temperature !== undefined ?
                                `${day.weather.temperature}` : 'N/A';

                            let dayHtml = `
                            <div class="day">
                                <div class="day-title mb-0 mt-5">{{ __('dashboard.day') }} ${index + 1} - <small>${day.date}</small>
                                    <span style="direction:ltr" class="badge bg-info text-dark">${weatherForecast} (${weatherTemperature})</span>
                                </div>
                                <div class="collapse-div">
                                    <p data-bs-toggle="collapse" href="#morning-${index}" role="button" aria-expanded="false" aria-controls="morning-${index}">
                                        <a class="btn btn-warning" data-bs-toggle="collapse" href="#morning-${index}" role="button" aria-expanded="false" aria-controls="morning-${index}">
                                            1
                                        </a>
                                        <span>{{ __('dashboard.morning') }}</span>
                                    </p>
                                    <div class="collapse" id="morning-${index}">
                                        <div class="card mb-3">
                                            ${day.morning.map(activity => `
                                                            <div class="row g-0">
                                                                <div class="col-md-8">
                                                                    <div class="card-body">
                                                                        <ul>
                                                                            <li>
                                                                                <h3>${activity.name}</h3> ${activity.description}
                                                                                <br>
                                                                                <a style="display:block;width:100%" href="https://www.google.com/maps?q=${activity.lat},${activity.lng}" target="_blank">
                                                                                    <i class="fa fa-map-marker"></i>
                                                                                    ${activity.location_name}
                                                                                </a>
                                                                                <hr>
                                                                                <h3>{{ __('dashboard.Activities') }}</h3>
                                                                                <ul>
                                                                                    ${activity.activities.map(act => `<li>${act.name}</li>`).join('')}
                                                                                </ul>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    ${activity.photo ? `<img style="width:100%;height:200px;object-fit:contain" src="${activity.photo}" class="img-fluid" alt="Place Image">` : ``}
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <hr>
                                                            <p><strong>{{ __('dashboard.Breakfast') }}</strong>: ${day.breakfast.name} ({{ __('dashboard.rating') }} ${day.breakfast.rating})
                                                            <br>
                                                            ${day.breakfast.photo ? `<img style="width:100%;height:200px;object-fit:contain" src="${day.breakfast.photo}" class="img-fluid" alt="Place Image">` : ``}
                                                            <br>
                                                            <a style="display:block;width:100%" href="https://www.google.com/maps?q=${day.breakfast.lat},${day.breakfast.lng}" target="_blank">
                                                                <i class="fa fa-map-marker"></i>
                                                                ${day.breakfast.location_name}
                                                            </a>
                                                        </p>
                                                                    </div>
                                                            </div>
                                                        `).join('')}
                                        </div>
                                    </div>
                                </div>
                                <div class="collapse-div">
                                    <p data-bs-toggle="collapse" href="#afternoon-${index}" role="button" aria-expanded="false" aria-controls="afternoon-${index}">
                                        <a class="btn btn-warning" data-bs-toggle="collapse" href="#afternoon-${index}" role="button" aria-expanded="false" aria-controls="afternoon-${index}">
                                            2
                                        </a>
                                        <span>{{ __('dashboard.afternoon') }}</span>
                                    </p>
                                    <div class="collapse" id="afternoon-${index}">
                                        <div class="card mb-3">
                                            ${day.afternoon.map(activity => `
                                                            <div class="row g-0">
                                                                <div class="col-md-8">
                                                                    <div class="card-body">
                                                                        <ul>
                                                                            <li>
                                                                                <h3>${activity.name}</h3> ${activity.description}
                                                                                <br>
                                                                                <a style="display:block;width:100%" href="https://www.google.com/maps?q=${activity.lat},${activity.lng}" target="_blank">
                                                                                    <i class="fa fa-map-marker"></i>
                                                                                    ${activity.location_name}
                                                                                </a>
                                                                                <hr>
                                                                                <h3>{{ __('dashboard.Activities') }}</h3>
                                                                                <ul>
                                                                                    ${activity.activities.map(act => `<li>${act.name}</li>`).join('')}
                                                                                </ul>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    ${activity.photo ? `<img style="width:100%;height:200px;object-fit:contain" src="${activity.photo}" class="img-fluid" alt="Place Image">` : ``}
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <hr>
                                                        <p><strong>{{ __('dashboard.Lunch') }}</strong>: ${day.lunch.name} ({{ __('dashboard.rating') }} ${day.lunch.rating})
                                                        <br>
                                                        ${day.lunch.photo ? `<img style="width:100%;height:200px;object-fit:contain" src="${day.lunch.photo}" class="img-fluid" alt="Place Image">` : ``}
                                                        <br>
                                                        <a style="display:block;width:100%" href="https://www.google.com/maps?q=${day.lunch.lat},${day.lunch.lng}" target="_blank">
                                                            <i class="fa fa-map-marker"></i>
                                                            ${day.lunch.location_name}
                                                        </a>
                                                    </p>
                                                                    </div>
                                                            </div>
                                                        `).join('')}
                                        </div>
                                    </div>
                                </div>
                                <div class="collapse-div">
                                    <p data-bs-toggle="collapse" href="#evening-${index}" role="button" aria-expanded="false" aria-controls="evening-${index}">
                                        <a class="btn btn-warning" data-bs-toggle="collapse" href="#evening-${index}" role="button" aria-expanded="false" aria-controls="evening-${index}">
                                            3
                                        </a>
                                        <span>{{ __('dashboard.evening') }}</span>
                                    </p>
                                    <div class="collapse" id="evening-${index}">
                                        <div class="card mb-3">
                                            ${day.evening.map(activity => `
                                                            <div class="row g-0">
                                                                <div class="col-md-8">
                                                                    <div class="card-body">
                                                                        <ul>
                                                                            <li>
                                                                                <h3>${activity.name}</h3> ${activity.description}
                                                                                <br>
                                                                                <a style="display:block;width:100%" href="https://www.google.com/maps?q=${activity.lat},${activity.lng}" target="_blank">
                                                                                    <i class="fa fa-map-marker"></i>
                                                                                    ${activity.location_name}
                                                                                </a>
                                                                                <hr>
                                                                                <h3>{{ __('dashboard.Activities') }}</h3>
                                                                                <ul>
                                                                                    ${activity.activities.map(act => `<li>${act.name}</li>`).join('')}
                                                                                </ul>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    ${activity.photo ? `<img style="width:100%;height:200px;object-fit:contain" src="${activity.photo}" class="img-fluid" alt="Place Image">` : ``}
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <hr>
                                                        <p><strong>{{ __('dashboard.Dinner') }}</strong>: ${day.dinner.name} ({{ __('dashboard.rating') }} ${day.dinner.rating})
                                                        <br>
                                                        ${day.dinner.photo ? `<img style="width:100%;height:200px;object-fit:contain" src="${day.dinner.photo}" class="img-fluid" alt="Place Image">` : ``}
                                                        <br>
                                                        <a style="display:block;width:100%" href="https://www.google.com/maps?q=${day.dinner.lat},${day.dinner.lng}" target="_blank">
                                                            <i class="fa fa-map-marker"></i>
                                                            ${day.dinner.location_name}
                                                        </a>
                                                    </p>
                                                                    </div>
                                                            </div>
                                                        `).join('')}
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                            $('#visit-places .days').append(dayHtml);

                            // Add markers for all activities
                            day.morning.forEach(activity => {
                                addMarker(activity.lat, activity.lng, activity.name);
                            });
                            day.afternoon.forEach(activity => {
                                addMarker(activity.lat, activity.lng, activity.name);
                            });
                            day.evening.forEach(activity => {
                                addMarker(activity.lat, activity.lng, activity.name);
                            });
                            addMarker(day.breakfast.lat, day.breakfast.lng, day.breakfast.name);
                            addMarker(day.lunch.lat, day.lunch.lng, day.lunch.name);
                            addMarker(day.dinner.lat, day.dinner.lng, day.dinner.name);
                        });

                        $('#visit-places').show();
                    } else {
                        console.error('Unexpected response format:', response);
                        alert('No itinerary data available.');
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading-spinner').hide();
                    console.error('Error fetching itinerary:', error);
                    alert('Failed to fetch itinerary data. Please try again.');
                }
            });
        });
    </script>
@endsection
