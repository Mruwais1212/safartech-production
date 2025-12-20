@extends('website.layout')
@section('title', __('dashboard.Page Not Found'))
@section('content')
    <div style="background-image: url('/site/img/plan.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">
        <div class="background without-waves"></div> <!-- div of shadow and waves -->
        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-12 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">404</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                    <h1 class="display-1">404</h1>
                    <h1 class="mb-4">{{ __('dashboard.Page Not Found') }}</h1>
                    <p class="mb-4">
                        {{ __('dashboard.We’re sorry, the page you have looked for does not exist in our website! Maybe go to our home page or try to use a search') }}
                    </p>
                    <a class="btn btn-warning py-3 px-5" href="/">{{ __('dashboard.Go Back To Home') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
