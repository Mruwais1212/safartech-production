@extends('website.layout')
@section('title', __('dashboard.AI Trip Planner'))
@section('content')
    <div style="background-image: {{ url('site/img/ai.jpg') }};background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">

                            @if (app()->getLocale() == 'ar')
                                <h1 class="display-5 animated fadeIn mb-4 text-white">رحلتك الى {{ isset($tour['city_name']) ? $tour['city_name'] : '' }} لمدة
                                    {{ isset($tour['days_count']) ? $tour['days_count'] : '' }} يوم</h1>
                            @else
                                <h1 class="display-5 animated fadeIn mb-4 text-white">Your Trip to {{ isset($tour['city_name']) ? $tour['city_name'] : '' }} For
                                    {{ isset($tour['days_count']) ? $tour['days_count'] : '' }} Days</h1>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- steps form -->

    <?php $data = json_decode($reservation->tour_data, true);
    if (!empty($data) && is_array($data)) {
        $tour = $data[0];
    } else {
        $tour = null;
    }
    ?>
    @if ($tour)
        <div class="container hotel-single">
            <div class="row">
                <div class="col-md-12">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="plan-section mt-4">
                                    {{-- @if (isset($data['photos']) && count($data['photos']) > 0)
                                    <div class="plan-gallery">
                                        <div class="container p-0">
                                            <div class="row justify-content-center">
                                                <div class="col col-md-12 gallery-container-wrap position-relative">
                                                    <div class="gallery-container" id="gallery-dynamic-thumbnails">
                                                        @foreach ($data['photos'] as $key => $photo)
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
                                @endif --}}

                                    <div class="plan-actions">
                                        <div class="plan-title">
                                            @if (app()->getLocale() == 'ar')
                                                <h1>رحلتك الى {{ isset($tour['city_name']) ? $tour['city_name'] : '' }} لمدة
                                                    {{ isset($tour['days_count']) ? $tour['days_count'] : '' }} يوم</h1>
                                            @else
                                                <h1>Your Trip to {{ isset($tour['city_name']) ? $tour['city_name'] : '' }}
                                                    For
                                                    {{ isset($tour['days_count']) ? $tour['days_count'] : '' }} Days</h1>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="ai-content">
                                        <div class="title text-dark fw-bold mb-2">
                                            {{ __('dashboard.Overview') }}
                                        </div>
                                        <p>
                                            {{ isset($tour['city_desc']) ? $tour['city_desc'] : '' }}
                                        </p>

                                        <div class="map-iframe">
                                            <div id="map" style="height: 500px; width: 100%;"></div>
                                        </div>
                                    </div>

                                    <div class="ai-visit-places" id="visit-places">
                                        <div class="title text-dark fw-bold mb-2">{{ __('dashboard.Places To Visit') }}
                                        </div>
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
    @endif
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
            var id = {{ $reservation->id }};
            $.ajax({
                url: '/ai-trip-latest-planner/' + id,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loading-spinner').hide();
                    $('.all-row').show();
                    if (response && response.length > 0 && response[0].itinerary) {
                        const itinerary = response[0].itinerary;
                        $('#visit-places .days').empty();
                        const firstActivity = itinerary[0].morning[0] || itinerary[0].afternoon[0] ||
                            itinerary[0].evening[0];
                        const firstActivityLat = firstActivity ? firstActivity.lat : 40.730610;
                        const firstActivityLng = firstActivity ? firstActivity.lng : -73.935242;
                        initMap(firstActivityLat, firstActivityLng);
                        itinerary.forEach((day, index) => {
                            const weatherForecast = day.weather?.forecast || day.weather
                                ?.description;
                            const weatherTemperature = day.weather?.temperature !== undefined ?
                                `${day.weather.temperature}°C` : 'N/A';
                            let dayHtml = `
                            <div class="day">
                                <div class="day-title mb-0 mt-5">{{ __('dashboard.day') }} ${index + 1} - <small>${day.date}</small>
                                    <span class="badge bg-info text-dark">${weatherForecast} (${weatherTemperature})</span>
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
                                                            </div>
                                                        `).join('')}
                                        </div>
                                    </div>
                                </div>
                                <div class="collapse-div">
                                    <p data-bs-toggle="collapse" href="#meals-${index}" role="button" aria-expanded="false" aria-controls="meals-${index}">
                                        <a class="btn btn-warning" data-bs-toggle="collapse" href="#meals-${index}" role="button" aria-expanded="false" aria-controls="meals-${index}">
                                            4
                                        </a>
                                        <span>{{ __('dashboard.meals') }}</span>
                                    </p>
                                    <div class="collapse" id="meals-${index}">
                                        <div class="card mb-3">
                                            <div style="{{ app()->getLocale() == 'ar' ? 'direction:rtl' : 'direction:ltr' }}" class="card-body">
                                                <p><strong>{{ __('dashboard.Breakfast') }}</strong>: ${day.breakfast.name} ({{ __('dashboard.rating') }} ${day.breakfast.rating})
                                                    <br>
                                                    ${day.breakfast.photo ? `<img style="width:100%;height:200px;object-fit:contain" src="${day.breakfast.photo}" class="img-fluid" alt="Place Image">` : ``}
                                                    <br>
                                                    <a style="display:block;width:100%" href="https://www.google.com/maps?q=${day.breakfast.lat},${day.breakfast.lng}" target="_blank">
                                                        <i class="fa fa-map-marker"></i>
                                                        ${day.breakfast.location_name}
                                                    </a>
                                                </p>
                                                <p><strong>{{ __('dashboard.Lunch') }}</strong>: ${day.lunch.name} ({{ __('dashboard.rating') }} ${day.lunch.rating})
                                                    <br>
                                                    ${day.lunch.photo ? `<img style="width:100%;height:200px;object-fit:contain" src="${day.lunch.photo}" class="img-fluid" alt="Place Image">` : ``}
                                                    <br>
                                                    <a style="display:block;width:100%" href="https://www.google.com/maps?q=${day.lunch.lat},${day.lunch.lng}" target="_blank">
                                                        <i class="fa fa-map-marker"></i>
                                                        ${day.lunch.location_name}
                                                    </a>
                                                </p>
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
