@php
    $setting = App\Models\Panel\Setting::whereIn('code', [
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'phone',
        'email',
        'commercial_registration','company_name'
    ])
        ->pluck('value', 'code')
        ->toArray();
@endphp
<div class="container-fluid text-white-50 footer pt-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-3">
        <div class="row justify-content-md-between">
            <div class="col-lg-3 col-md-6">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('site/img/logo.png') }}" alt="Icon">
                </div>
                <p class="mb-2 text-white footer-desc">
                    {{ __('dashboard.Unleash Your Wanderlust') }}
                    <strong>{{ __('dashboard.Explore the World with Us') }}</strong>
                </p>
                <div class="d-flex pt-2">
                    <a class="btn btn-outline-light btn-social" href="{{ $setting['facebook'] }}"><i
                            class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social" href=" {{ $setting['twitter'] }}"><i
                            class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light btn-social" href=" {{ $setting['instagram'] }}"><i
                            class="fab fa-instagram"></i></a>
                    <a class="btn btn-outline-light btn-social" href=" {{ $setting['linkedin'] }}"><i
                            class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-outline-light btn-social" href=" {{ $setting['youtube'] }}"><i
                            class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 links">
                <p class="text-warning list-title mb-3">{{ __('dashboard.About Us') }}</p>
                <a class="btn btn-link text-white-50"
                    href="/terms-and-conditions">{{ __('dashboard.Terms and conditions') }}</a>
                <a class="btn btn-link text-white-50" href="/privacy-policy">{{ __('dashboard.Privacy Policy') }}</a>
            </div>
            <div class="col-lg-2 col-md-6 links">
                <p class="text-warning list-title mb-3">{{ __('dashboard.Services') }}</p>
                <a class="btn btn-link text-white-50" href="javascript:void(0)">{{ __('dashboard.Flight reservation') }}</a>
                <a class="btn btn-link text-white-50" href="javascript:void(0)">{{ __('dashboard.Hotel reservation') }}</a>
            </div>
            <div class="col-lg-2 col-md-6 links">
                <p class="text-warning list-title mb-3">{{ __('dashboard.Portfolio') }}</p>
                <a class="btn btn-link text-white-50" href="/saved-trips">{{ __('dashboard.Saved plan') }}</a>
                <a class="btn btn-link text-white-50" href="/my-trips">{{ __('dashboard.Reservations') }}</a>
            </div>
            <div class="col-lg-2 col-md-6 links contact-footer">
                <p class="text-warning list-title mb-3">{{ __('dashboard.Contact us') }}</p>
                <a class="btn btn-link text-white" href="mailto:{{ $setting['email'] }}">
                    <i class="far fa-envelope"></i>
                    {{ $setting['email'] }}
                </a>
                <a class="btn btn-link text-white" href="tel:+{{ $setting['phone'] }}" target="_blank">
                    <i class="bi bi-telephone"></i>
                    {{ $setting['phone'] }}
                </a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-12 text-center mb-3 mb-md-0">
                    @if (app()->getLocale() == 'ar')
                        حقوق النشر © {{ date('Y') }} سفرتك | جميع الحقوق محفوظة
                    @else
                        Copyright © {{ date('Y') }} SaferTech | All Rights Reserved
                    @endif
                    <br>
                    {{ __('trans.Trade_name'). ': ' . ($setting['company_name'] ?? '') }}
                     .
                    {{ __('trans.License_category'). ': ' .__('trans.an_agency') }}
                     .
                    {{ __('trans.License_number'). ': ' . ($setting['commercial_registration'] ?? '') }}
                </div>
            </div>
        </div>
    </div>
</div>
