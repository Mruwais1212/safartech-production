@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/trip.png');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.Confirmation') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- steps form -->
    <div class="container prepare-page">
        @php
            $reservationn = \App\Models\Reservation::find($reservation->id);
        @endphp
        <div class="row">
            <div class="col-md-12">
                <!-- <form class="steps-form"> -->
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10 text-center mb-5">
                            <div class="form-inputs p-0 m-0 text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.2s">
                                <img width="300" src="/site/img/success.png" alt="success">
                                <h2 class="prepare-title">{{ __('dashboard.Reservation') }} : #{{ $reservationn->uuid }}
                                </h2>
                                <h6 style="width: 70%;" class="propare-desc">
                                    {{ __('dashboard.Your reservation has been Booked successfully', [
                                        'reservation_number' => '#' . $reservationn->uuid,
                                        'type' => $reservation->getType(),
                                    ]) }}
                                </h6>
                            </div>
                            <a href="/my-trips/{{ $reservation->id }}" class="btn btn-primary btn-lg btn-block">
                                {{ __('dashboard.See The reservation') }}
                            </a>
                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>
    <!-- Steps form End -->

    <script>
        // Clear localStorage when success page loads
        document.addEventListener('DOMContentLoaded', function() {
            localStorage.clear();
            console.log('LocalStorage cleared on success page load');
        });
    </script>
@endsection
