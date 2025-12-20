@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
    <div style="background-image: url('site/img/sign-up.png');" class="auth-page">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="steps-form" action='/sign-up' method='post'>
                        @csrf
                        <div class="container">
                            <div class="row justify-content-center" style="margin-top: 68px;">
                                <div class="col-md-8 step-form-bg">
                                    <div class="text-center mx-auto mb-3 wow fadeInUp" data-wow-delay="0.1s"
                                        style="max-width: 100%;">
                                        <h4 class="section-title mt-4">{{ __('dashboard.sign_up') }}</h4>
                                    </div>
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-md-11">
                                            <div class="form-inputs bg-black text-center mx-auto mb-2 wow fadeInUp"
                                                data-wow-delay="0.2s">
                                                <div class="container">
                                                    <div class="row ">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">{{ __('dashboard.name') }}</label>
                                                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="{{ __('dashboard.name') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">{{ __('dashboard.email') }}</label>
                                                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('dashboard.email') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">{{__('dashboard.password')}}</label>
                                                                <input type="password" class="form-control" name="password" placeholder="{{__('dashboard.password')}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">{{__('dashboard.confirm_password')}}</label>
                                                                <input type="password" class="form-control" name="password_confirmation" placeholder="{{__('dashboard.confirm_password')}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">{{__('dashboard.phone')}}</label>
                                                                <input type="text" class="form-control" value="{{ old('phone') }}" name="phone"
                                                                    placeholder="{{__('dashboard.phone')}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">

                                                            <div class="form-group">
                                                                <label class="d-block"
                                                                    for="exampleInputEmail1">{{__('dashboard.gender')}}</label>
                                                                <div class="radios">
                                                                    <div class="form-check">
                                                                        <input type="radio" class="form-check-input"
                                                                            value="male" name="gender" {{ old('gender')=='male' ? 'checked' : '' }}
                                                                            id="exampleCheck1">
                                                                        <label class="form-check-label"
                                                                            for="exampleCheck1">{{__('dashboard.male')}}</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input type="radio" class="form-check-input"
                                                                            value="female" name="gender" {{ old('gender')=='female' ? 'checked' : '' }}
                                                                            id="exampleCheck12">
                                                                        <label class="form-check-label"
                                                                            for="exampleCheck12">{{__('dashboard.female')}}</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">

                                                            <div class="radios">
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input" {{ old('accept') == 1 ? 'checked' : ''}} name="accept" id="exampleCheck12">
                                                                    <label class="form-check-label" for="exampleCheck12">
                                                                        <div class="auth-link text-start mt-1">
                                                                            {{__('dashboard.By signing up you agree to our')}}

                                                                            <a class="d-inline-block w-auto" href="/terms-and-conditions">
                                                                             {{__('dashboard.Terms and conditions')}}
                                                                            </a>

                                                                            &

                                                                            <a class="d-inline-block w-auto" href="/privacy-policy">
                                                                                {{__('dashboard.Privacy policy')}}
                                                                            </a>
                                                                        </div>
                                                                    </label>

                                                                </div>


                                                            </div>

                                                        </div>
                                                    </div>

                                                    <button type="submit"
                                                        class="submit-btn btn btn-warning animated fadeIn">
                                                        {{__('dashboard.sign_up')}}
                                                    </button>
                                                    <p class="auth-link">
                                                        {{__('dashboard.already_have_an_account')}}
                                                        <a href="/sign-in">
                                                            {{__('dashboard.sign_in')}}
                                                        </a>
                                                    </p>
                                                </div>
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
