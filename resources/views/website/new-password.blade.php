@extends('website.layout')
@section('title', __('dashboard.new_password'))
@section('content')
    <div style="background-image: url('/site/img/reset.png');" class="auth-page">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="steps-form" action="/reset-password" method="post">
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-6 step-form-bg">
                                    <div class="text-center mx-auto mb-3 wow fadeInUp" data-wow-delay="0.1s"
                                        style="max-width: 100%;">
                                        <h4 class="section-title mt-4">{{ __('dashboard.new_password') }}</h4>
                                        <p class="auth-subtitle">
                                            {{ __('dashboard.Create a new password. Ensure it differs from previous ones for security') }}
                                        </p>
                                    </div>
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-md-8">
                                            <div class="form-inputs bg-black text-center mx-auto mb-2 wow fadeInUp"
                                                data-wow-delay="0.2s">
                                                <div class="container">
                                                    <div class="row">
                                                        <input type="hidden" name="token" value="{{ request('token') }}">
                                                        <input type="hidden" name="email" value="{{ request('email') }}">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="password">{{ __('dashboard.New password') }}</label>
                                                                <input type="password" class="form-control" id="password"
                                                                    name="password"
                                                                    placeholder="{{ __('dashboard.New password') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="password">{{ __('dashboard.Confirm password') }}</label>
                                                                <input type="password" class="form-control" id="password"
                                                                    name="password_confirmation"
                                                                    placeholder="{{ __('dashboard.Confirm password') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="submit-btn btn btn-warning animated fadeIn">
                                                    {{ __('dashboard.Update my password') }}
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
