@extends('website.layout')
@section('title', __('dashboard.dalely'))
@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/hotels.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.dalely') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- steps form -->
    <div class="container hotel-single reservations-page">
        <div class="row">
            <div class="col-md-12">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="plan-section mt-4">
                                <div class="rooms search-page">
                                    <div class="rooms-filter">
                                        <!-- Filter Controls -->
                                        <div class="rooms-filter-header">
                                            <h1>{{ __('dashboard.dalely') }}</h1>
                                            <div class="nav-filter-rooms">

                                            </div>
                                        </div>
                                        <!-- Filterable Items -->
                                        <div class="hotels-cards rooms-cards">
                                            <div class="row">
                                                @foreach ($reservations as $reservation)
                                                    <?php $data = json_decode($reservation->tour_data, true);
                                                    if (!empty($data) && is_array($data)) {
                                                        $tour = $data[0];
                                                    } else {
                                                        $tour = null;
                                                    }
                                                    ?>
                                                    @if($tour)
                                                    <div class="col-xxl-3 col-lg-4 col-md-6 wow fadeInUp"
                                                        data-wow-delay="0.1s">
                                                        <div class="property-item overflow-hidden">
                                                            <div
                                                                class="bg-white text-primary d-flex justify-content-center align-items-center py-3 px-3">
                                                                @if (app()->getLocale() == 'ar')
                                                                <p class="hotel-country m-0">رحلتك الى
                                                                    {{ isset($tour['city_name']) ? $tour['city_name'] : '' }}
                                                                    لمدة
                                                                    {{ isset($tour['days_count']) ? $tour['days_count'] : '' }}
                                                                    يوم</p>
                                                            @else
                                                                <p class="hotel-country m-0">Your Trip to
                                                                    {{ isset($tour['city_name']) ? $tour['city_name'] : '' }}
                                                                    For
                                                                    {{ isset($tour['days_count']) ? $tour['days_count'] : '' }}
                                                                    Days</p>
                                                            @endif
                                                            </div>
                                                            <div class="px-4 py-2 pb-0">
                                                                <p style="height: auto" class="hotel-desc text-center">
                                                                    {{ __('dashboard.from') . ': ' }}
                                                                    {{ \Carbon\Carbon::parse($reservation['date_from'])->toDateString() }}
                                                                    --
                                                                    {{ __('dashboard.to') . ': ' }}
                                                                    {{ \Carbon\Carbon::parse($reservation['date_to'])->toDateString() }}
                                                                </p>
                                                            </div>
                                                            <div
                                                                class="px-4 py-2 d-flex justify-content-between align-items-center border-top hotel-bottom">
                                                                {{-- <small class="py-2">
                                                                {{ __('dashboard.Starting from') }} :
                                                                <br>
                                                                <h5>{{ $trip->price }} {{ $trip->currency }}</h5>
                                                            </small> --}}
                                                                <small class="py-2 w-100">
                                                                    <a href="/latest-ai-trip-planner/{{ $reservation->id }}"
                                                                        class="btn btn-warning py-2 w-100 px-3">{{ __('dashboard.view_details') }}</a>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endforeach
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
