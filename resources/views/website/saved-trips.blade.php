@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
    <div style="background-image: url('/site/img/trip.png');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white"> {{ __('dashboard.Saved Plans') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
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
                                            {{ __('dashboard.Your Saved Plans') }}</h1>
                                    </div>
                                    @foreach ($trips as $trip)
                                        <div class="col-xxl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                            <div class="property-item overflow-hidden">
                                                <div
                                                    class="bg-white text-primary d-flex justify-content-between align-items-center py-3 px-3">
                                                    <p class="hotel-country m-0">
                                                        {{ app()->getLocale() == 'ar' ? $trip->country->name_ar : $trip->country->name_en }}
                                                        ,
                                                        {{ app()->getLocale() == 'ar' ? $trip->city->name_ar : $trip->city->name_en }}
                                                    </p>
                                                </div>
                                                <div class="position-relative overflow-hidden">
                                                    <a href="/trip-details/{{ $trip->id }}">
                                                        <img class="img-fluid"
                                                            src="{{ is_file('uploads/' . $trip->image) ? asset('uploads/' . $trip->image) : '/site/img/property-1.jpg' }}"alt="">
                                                    </a>
                                                </div>
                                                <div class="px-4 py-2 pb-0">
                                                    <div
                                                        class="hotel-title text-warning mb-3 d-flex justify-content-between align-items-center">
                                                        <div class="align-items-center group-travel-interests">
                                                            @foreach ($trip->travelInterests as $interest)
                                                                <p class="travel-interests">
                                                                    {{ app()->getLocale() == 'ar' ? $interest->name_ar : $interest->name_en }}
                                                                </p>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <p class="hotel-desc">
                                                        {{ app()->getLocale() == 'ar' ? $trip->description_ar : $trip->description_en }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="px-4 py-2 d-flex justify-content-between align-items-center border-top hotel-bottom">
                                                    <small class="py-2">
                                                        {{ __('dashboard.Starting from') }}:
                                                        <br>
                                                        <h5>{{ $trip->price }} {{ $trip->currency }}</h5>
                                                    </small>
                                                    <small class="py-2">
                                                        <a href="/trips/{{ $trip->id }}"
                                                            class="btn btn-warning py-2 px-3">{{ __('dashboard.view_details') }}</a>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- Property List End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
