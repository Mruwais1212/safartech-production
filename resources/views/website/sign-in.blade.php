@extends('website.layout')
@section('title', __('dashboard.sign_in'))
@section('content')
    <div style="background-image: url('site/img/login.jpg');" class="auth-page">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="steps-form" action="/sign-in" method="post">
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-6 step-form-bg">
                                    <div class="text-center mx-auto mb-3 wow fadeInUp" data-wow-delay="0.1s"
                                        style="max-width: 100%;">
                                        <h4 class="section-title mt-4">{{ __('dashboard.sign_in') }}</h4>
                                    </div>
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-md-10">
                                            <div class="form-inputs bg-black text-center mx-auto mb-2 wow fadeInUp"
                                                data-wow-delay="0.2s">
                                                <div class="container">
                                                    <div class="row ">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="exampleInputEmail1">{{ __('dashboard.email') }}</label>
                                                                <input type="email" class="form-control" name="email"
                                                                    placeholder="{{ __('dashboard.email') }}"
                                                                    value="{{ old('email') }}" required>
                                                                @error('email')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label
                                                                    for="exampleInputEmail1">{{ __('dashboard.password') }}</label>
                                                                <input type="password" class="form-control" name="password"
                                                                    placeholder="{{ __('dashboard.password') }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <a class="forget_password" href="/forgot-password">
                                                                {{ __('dashboard.forget_password') }}
                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>

                                                <button type="submit" class="submit-btn btn btn-warning animated fadeIn">
                                                    {{ __('dashboard.sign_in') }}
                                                </button>

                                                <p class="auth-link">
                                                    {{ __('dashboard.dont_have_account') }}
                                                    <a href="/sign-up">
                                                        {{ __('dashboard.sign_up') }}
                                                    </a>
                                                </p>
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
