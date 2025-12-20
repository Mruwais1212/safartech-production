@extends('website.layout')
@section('title', __('dashboard.Payment Error'))
@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/trip.png');background-position-y:center ;" class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{__('dashboard.Payment Error')}}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- steps form -->
    <div class="container prepare-page">
        <div class="row">
            <div class="col-md-12">
                <!-- <form class="steps-form"> -->
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10 text-center mb-5">
                            <div class="form-inputs p-0 m-0 text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.2s" style="display: flex; flex-direction: column; align-items: center;">
                                {{-- <img width="300" src="{{public_path('/site/img/error.png')}}"   alt="error"> --}}
                             
                              <svg style="width: 300px; margin:auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#f67c36" d="M320 576C178.6 576 64 461.4 64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576zM320 384C302.3 384 288 398.3 288 416C288 433.7 302.3 448 320 448C337.7 448 352 433.7 352 416C352 398.3 337.7 384 320 384zM320 192C301.8 192 287.3 207.5 288.6 225.7L296 329.7C296.9 342.3 307.4 352 319.9 352C332.5 352 342.9 342.3 343.8 329.7L351.2 225.7C352.5 207.5 338.1 192 319.8 192z"/></svg>
                              @if(session('error_message'))
                                    <h6 style=" margin:auto" class="propare-desc">{{ session('error_message') }}</h6>
                                @else
                                    <h6 style=" margin:auto" class="propare-desc">{{__('dashboard.Sorry for the payment error')}}</h6>
                                @endif
                            </div>
                            <a href="/" class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn">
                                {{__('dashboard.back to home')}}
                            </a>
                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>
    <!-- Steps form End -->
@endsection
