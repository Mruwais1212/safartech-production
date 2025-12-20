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
                <form class="steps-form">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="progress-container text-center">
                                    <!-- <div class="progress" id="progress" style="width:0%;"></div> first step -->
                                    <!-- <div class="progress" id="progress" style="width:30%;"></div> second step -->
                                    <div class="progress" id="progress" style="width:60%;"></div>
                                    <!-- <div class="progress" id="progress" style="width:90%;"></div> last step-->

                                    <div class="text-wrap finished">
                                        <div class="circle">01</div>
                                        <p class="text">{{ __('dashboard.Plan') }}</p>
                                    </div>
                                    <div class="text-wrap finished">
                                        <div class="circle">02</div>
                                        <p class="text">{{ __('dashboard.Compare') }}</p>
                                    </div>
                                    <div class="text-wrap active">
                                        <div class="circle">03</div>
                                        <p class="text">{{ __('dashboard.Select') }}</p>
                                    </div>
                                    <div class="text-wrap">
                                        <div class="circle">04</div>
                                        <p class="text">{{ __('dashboard.Payment') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="plan-section">

                                    <div class="plan-gallery">
                                        <div class="container p-0">
                                            <div class="row justify-content-start">
                                                <div class="col col-md-12 gallery-container-wrap position-relative">
                                                    <div class="gallery-container" id="gallery-dynamic-thumbnails">
                                                        <a class="gallery-item" data-index="0"
                                                            data-src="{{ is_file('uploads/' . $trip->image) ? asset('uploads/' . $trip->image) : '/site/img/property-1.jpg' }}">
                                                            <img alt="layers of blue." class="img-responsive"
                                                                src="{{ is_file('uploads/' . $trip->image) ? asset('uploads/' . $trip->image) : '/site/img/property-1.jpg' }}" />
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="plan-actions">
                                        <div class="plan-title">
                                            <h1>
                                                {{ app()->getLocale() == 'ar' ? $trip->country->name_ar : $trip->country->name_en }}
                                                ,
                                                {{ app()->getLocale() == 'ar' ? $trip->city->name_ar : $trip->city->name_en }}
                                            </h1>
                                        </div>
                                        <div class="btns">
                                            <a href="/choose/{{ $trip->id }}" class="btn btn-warning py-3 px-5"
                                                type="submit">
                                                {{ __('dashboard.Choose The plan') }}
                                            </a>
                                            <a href="{{ route('search.results') }}" class="btn btn-warning py-3 px-5">
                                                {{ __('dashboard.Back to Search Results') }}
                                            </a>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="plan-details">
                                        <div class="plan-details-title">
                                            <h6>{{ __('dashboard.Traveling details') }}</h6>
                                        </div>
                                        <p class="details">
                                            {{ app()->getLocale() == 'ar' ? $trip->description_ar : $trip->description_en }}
                                        </p>
                                    </div>

                                    <hr>
                                    {{-- <div class="plan-details">
                                        <div class="plan-details-title">
                                            <h6>{{ __('dashboard.Advices for this trip') }}</h6>
                                        </div>
                                        <ul class="details ps-3">
                                            <li>
                                                Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                                tellus dui
                                                purus
                                            </li>
                                            <li>
                                                Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                                tellus dui
                                                purus
                                            </li>
                                            <li>
                                                Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                                tellus dui
                                                purus
                                            </li>
                                            <li>
                                                Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                                tellus dui
                                                purus
                                            </li>
                                        </ul>
                                    </div> --}}
                                </div>
                            </div>

                            <!-- Property List Start -->
                            <div class="container py-5 places">
                                <div class="container">
                                    <div class="row g-4">
                                        <div class="text-start mx-auto wow fadeInUp p-0" data-wow-delay="0.1s"
                                            style="max-width: 100%;">
                                            <h1 class="mb-1 section-title pb-2 lined-title aftrer-title">
                                                {{ __('dashboard.Places To Visit') }}
                                            </h1>
                                        </div>
                                        @foreach ($trip->places as $place)
                                            <div class="col-lg-3 col-md-4 col-6 wow fadeInUp mb-3" data-wow-delay="0.1s">
                                                <div class="property-item place-item overflow-hidden mb-3">
                                                    <div class="position-relative overflow-hidden">
                                                        <a href=""><img style="height: 350px;" class="img-fluid"
                                                                src="{{ is_file('uploads/' . $place->image) ? asset('uploads/' . $place->image) : '/site/img/property-1.jpg' }}" alt=""></a>
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
                                        <div class="text-start mx-auto wow fadeInUp p-0" data-wow-delay="0.1s"
                                            style="max-width: 100%;">
                                            <h1 class="mb-1 section-title pb-2 lined-title aftrer-title">
                                                {{ __('dashboard.Related Trips') }}</h1>
                                        </div>
                                        @foreach ($relatedTrips as $relatedTrip)
                                            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                                <div class="property-item overflow-hidden">
                                                    <div
                                                        class="bg-white text-primary d-flex justify-content-between align-items-center py-3 px-3">
                                                        <p class="hotel-country m-0">
                                                            {{ app()->getLocale() == 'ar' ? $relatedTrip->country->name_ar : $relatedTrip->country->name_en }}
                                                            ,
                                                            {{ app()->getLocale() == 'ar' ? $relatedTrip->city->name_ar : $relatedTrip->city->name_en }}
                                                        </p>
                                                    </div>
                                                    <div class="position-relative overflow-hidden">
                                                        <a href="/trips/{{ $relatedTrip->id }}"><img class="img-fluid"
                                                                src="{{ is_file('uploads/' . $relatedTrip->image) ? asset('uploads/' . $relatedTrip->image) : '/site/img/property-1.jpg' }}" alt=""></a>
                                                    </div>
                                                    <div class="px-4 py-2 pb-0">
                                                        <p class="hotel-desc">
                                                            {{ app()->getLocale() == 'ar' ? $relatedTrip->description_ar : $relatedTrip->description_en }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="px-4 py-2 d-flex justify-content-between align-items-center border-top hotel-bottom">
                                                        <small class="py-2 w-100 text-center">
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
                            <!-- Property List End -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Steps form End -->
@endsection
