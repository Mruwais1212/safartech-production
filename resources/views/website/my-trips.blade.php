@extends('website.layout')
@section('title', __('dashboard.My Trips'))
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
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.My Trips') }}</h1>
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
                                            <h1>{{ __('dashboard.My Reservations') }}</h1>
                                            <div class="nav-filter-rooms">
                                                {{-- <span class="active"
                                                    data-filter>{{ __('dashboard.All reservations') }}</span>
                                                <span data-filter="one_bed">{{ __('dashboard.Current') }} </span>
                                                <span data-filter="two_beds">{{ __('dashboard.Canceled') }}</span>
                                                <span data-filter="expired">{{ __('dashboard.expired') }}</span> --}}
                                            </div>
                                        </div>
                                        <!-- Filterable Items -->

                                        <div class="hotels-cards rooms-cards">
                                            <div class="row">
                                                @foreach ($reservations as $reservation)
                                                    <div data-tags="one_bed" class="col-md-4">
                                                        <div class="card">
                                                            <div class="card-content">
                                                                <h2>
                                                                    {{ $reservation->getType() }}
                                                                </h2>
                                                                <hr class="my-2">
                                                                @if ($reservation->type == 1)
                                                                    <h6 class="title text-dark">{{ __('dashboard.Flight') }}
                                                                    </h6>
                                                                @endif
                                                                @if ($reservation->type == 1 || $reservation->type == 3)
                                                                    <ul class="hotel-list-options">
                                                                        <li>
                                                                            <i class="bi bi-plane"></i>
                                                                            {{ __('dashboard.from') }}
                                                                            {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                                                            ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                                                            {{ __('dashboard.to') }}
                                                                            {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                                                            ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                                                        </li>
                                                                    </ul>
                                                                @endif
                                                                @if ($reservation->type == 1)
                                                                    <h6 class="title text-dark">{{ __('dashboard.Hotel') }}
                                                                    </h6>
                                                                @endif
                                                                @if ($reservation->type == 1 || $reservation->type == 2)
                                                                    <ul class="hotel-list-options">
                                                                        <li>
                                                                            <i class="bi bi-building"></i>
                                                                            {{ @$reservation->hotel->hotel_name }}
                                                                        </li>
                                                                        <li>
                                                                            <i class="bi bi-calendar"></i>
                                                                            {{ @$reservation->hotel->check_in }} -
                                                                            {{ @$reservation->hotel->check_out }}
                                                                        </li>
                                                                    </ul>
                                                                @endif

                                                                <a href="/my-trips/{{ $reservation->id }}"
                                                                    class="btn btn-outline-warning py-2 mt-1 mb-2 px-5 w-100 animated fadeIn fw-bold">{{ __('dashboard.details') }}</a>
                                                            </div>
                                                        </div>
                                                    </div>
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
