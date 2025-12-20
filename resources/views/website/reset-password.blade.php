@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
    <div style="background-image: url('/site/img/reset.png');" class="auth-page">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="steps-form" method="POST" action="/forgot-password">
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-6 step-form-bg">
                                    <div class="text-center mx-auto mb-3 wow fadeInUp" data-wow-delay="0.1s"
                                        style="max-width: 100%;">
                                        <h4 class="section-title mt-4">{{ __('dashboard.Reset password') }}</h4>
                                        <p class="auth-subtitle">
                                            {{ __('dashboard.Enter your email and we will send you a link to reset your password') }}
                                        </p>
                                    </div>
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-md-8">
                                            <div class="form-inputs bg-black text-center mx-auto mb-2 wow fadeInUp"
                                                data-wow-delay="0.2s">
                                                <div class="container">
                                                    <div class="row ">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="exampleInputEmail1">{{ __('dashboard.email') }}</label>
                                                                <input type="email" class="form-control" name="email"
                                                                    placeholder="{{ __('dashboard.email') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="submit-btn btn btn-warning animated fadeIn">
                                                    {{ __('dashboard.Reset Password') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
