@extends('website.layout')
@section('title', __('dashboard.contact_us'))
@section('content')
    <div class="contact-page">
        <div class="contact-form">
            <form class="steps-form" action="/contact-us" method="post">
                @csrf
                <div class="mt-5">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.2s">
                                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s"
                                    style="max-width: 100%;">
                                    <h4 class="mb-3 section-title mt-4">{{ __('dashboard.contact_us') }}</h4>
                                </div>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">{{ __('dashboard.name') }}</label>
                                                        <input type="text" class="form-control" name="name"
                                                            placeholder="{{ __('dashboard.write_your_name_here') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="last_name">{{ __('dashboard.email_address') }}</label>
                                                        <input type="text" class="form-control" name="email"
                                                            placeholder="{{ __('dashboard.write_your_email_here') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="last_name">{{ __('dashboard.phone') }}</label>
                                                        <input type="text" class="form-control" name="phone"
                                                            placeholder="{{ __('dashboard.write_your_phone_here') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="last_name">{{ __('dashboard.message') }}</label>
                                                        <textarea rows="6" type="text" class="form-control" name="message"
                                                            placeholder="{{ __('dashboard.write_your_message_here') }}"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <button type="submit" class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn w-50">
                                    {{ __('dashboard.send_message') }}
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="contact-image-section">
            <div class="background"></div>

            <div class="contact-info">
                <h1>{{ __('dashboard.Get In Touch With Us Now') }}</h1>
                <ul class="contact-list">
                    <li><i class="fas fa-envelope"></i>{{App\Models\Panel\Setting::where('code', 'email')->first()->value}}</li>
                    <li><i class="fas fa-phone-alt"></i>{{App\Models\Panel\Setting::where('code', 'phone')->first()->value}}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
