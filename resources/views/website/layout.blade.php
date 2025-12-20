<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title> {{ __('dashboard.safar_tech') }} | @yield('title')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <!-- Favicon -->
    <link href="{{ asset('site/img/fav.png') }}" rel="icon">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap"
        rel="stylesheet">
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Libraries Stylesheet -->
    <link href="{{ asset('site/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('site/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- phone input plugin -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.9.3/build/css/intlTelInput.css">
    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('site/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('site/css/star-rating.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('site/css/daterangepicker.css') }}" />
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('site/css/asRange.min.css') }}" />
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('site/css/lightgallery-bundle.min.css') }}" />
    <link href="{{ asset('site/css/responsive.css') }}" rel="stylesheet">

    @if (app()->getLocale() == 'en')
        <link href="{{ asset('site/css/style.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('site/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('site/css/style-ar.css') }}" rel="stylesheet">
    @endif
    @yield('files')
    @section('newCss')
    @show
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-warning" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar Start -->
        <div class="container nav-bar bg-transparent">
            <nav class="navbar navbar-expand-lg navbar-light py-0 px-4">
                <a href="/" class="navbar-brand d-flex align-items-center text-center">
                    <div class="icon">
                        <img class="img-fluid" src="{{ asset('site/img/logo.png') }}" alt="Icon">
                    </div>
                </a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto me-auto">
                        <a href="/"
                            class="nav-item nav-link {{ Request::is('/') || Request::is('en') ? 'active' : '' }}">{{ __('dashboard.home') }}</a>
                        <a href="/about-us"
                            class="nav-item nav-link {{ Request::is('about-us') || Request::is('en/about-us') ? 'active' : '' }}">{{ __('dashboard.about_us') }}</a>
                        <a href="/ai-trip"
                            class="nav-item nav-link {{ Request::is('ai-trip') || Request::is('en/ai-trip') ? 'active' : '' }}">{{ __('dashboard.AI Trip Planner') }}</a>
                        <a href="/ai-trip-planner"
                            class="nav-item nav-link {{ Request::is('ai-trip-planner') || Request::is('en/ai-trip-planner') || Request::is('en/ai-trip-results') ? 'active' : '' }}">{{ __('dashboard.Yalla Plan') }}</a>
                    </div>
                    <div class="icon-menu">
                        <a href="{{ app()->getLocale() == 'ar' ? request()->fullUrlWithQuery(['lang' => 'en']) : request()->fullUrlWithQuery(['lang' => 'ar']) }}"
                            class="nav-item nav-link lang-link">
                            @if (app()->getLocale() == 'ar')
                                EN
                            @else
                                AR
                            @endif
                        </a>
                        @if (auth()->check())
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link icon-dropdown" data-bs-toggle="dropdown">
                                    <i class="bi bi-person"></i>
                                </a>
                                <div class="dropdown-menu m-0"
                                    style="{{ app()->getLocale() == 'ar' ? 'right: -232px;top: 70px' : 'right: -33px;top: 70px' }}">
                                    <span href="/property-list" class="dropdown-item profile-item">
                                        <span style="gap:15px"
                                            class="d-flex justify-content-between align-items-center">
                                            <img src="{{ file_exists('uploads/' . auth()->user()->photo) ? asset('uploads/' . auth()->user()->photo) : '/images/default_user.png' }}"
                                                alt="{{ auth()->user()->name }}"
                                                style="width: 35px;border-radius: 30px;" />
                                            <div>
                                                {{ auth()->user()->name }}
                                                <div class="profile-email">
                                                    {{ auth()->user()->email }}
                                                </div>
                                            </div>

                                        </span>
                                        <span class="d-flex align-items-center">
                                            <a href="/edit-profile" class="active">
                                                <i class="far fa-edit"></i>
                                            </a>
                                        </span>
                                    </span>
                                    <span class="dropdown-item">
                                        <hr />
                                    </span>
                                    <span class="dropdown-item">
                                        <span>
                                            <i class="bi bi-globe"></i>
                                            {{ __('dashboard.Language') }}
                                        </span>
                                        <span>
                                            <a href="{{ request()->url() }}?lang=en"
                                                class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                                            <a href="{{ request()->url() }}?lang=ar"
                                                class="{{ app()->getLocale() == 'ar' ? 'active' : '' }}">AR</a>
                                        </span>
                                    </span>
                                    <span class="dropdown-item">
                                        <span>
                                            <i class="bi bi-cash"></i>
                                            {{ __('dashboard.Currency') }}
                                        </span>
                                        <span>
                                            <a href="{{ request()->url() }}?currency=SAR"
                                                class="{{ session('currency') == 'SAR' ? 'active' : '' }}">SAR</a>
                                            <a href="{{ request()->url() }}?currency=USD"
                                                class="{{ session('currency') == 'USD' ? 'active' : '' }}">USD</a>
                                        </span>
                                    </span>
                                    <span class="dropdown-item">
                                        <hr />
                                    </span>
                                    {{-- <a href="/saved-plans" class="dropdown-item">
                                        <span>
                                            <i class="fas fa-plane"></i>
                                            {{ __('dashboard.Saved Plans') }}
                                        </span>
                                    </a> --}}
                                    <a href="/my-trips" class="dropdown-item">
                                        <span>
                                            <i class="fas fa-ticket-alt"></i>
                                            {{ __('dashboard.My Reservations') }}
                                        </span>
                                    </a>
                                    <a href="/contact-us" class="dropdown-item">
                                        <span>
                                            <i class="fas fa-headset"></i>
                                            {{ __('dashboard.Contact Us') }}
                                        </span>
                                    </a>
                                    <a href="/latest-ai-trip-planner" class="dropdown-item">
                                        <span>
                                            <i class="fas fa-file"></i>
                                            {{ __('dashboard.dalely') }}
                                        </span>
                                    </a>
                                    {{-- <a href="/previous-visited" class="dropdown-item">
                                        <span>
                                            <i class="fas fa-globe-americas"></i>
                                            {{ __('dashboard.previous visited') }}
                                        </span>
                                    </a> --}}
                                    <span class="dropdown-item">
                                        <hr />
                                    </span>
                                    <a class="dropdown-item d-flex align-items-center" style="cursor: pointer;"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <span> <i class="bi bi-box-arrow-left"></i>
                                            {{ __('dashboard.Sign Out') }}
                                        </span>
                                    </a>

                                    <form id="logout-form" action="/sign-out" method="POST">
                                        @csrf
                                        @method('POST')
                                        <input type="submit" value="Sign Out" style="display: none;" />
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link icon-dropdown" data-bs-toggle="dropdown">
                                    <i class="bi bi-justify"></i>
                                </a>
                                <div class="dropdown-menu m-0" style="right: -60px;top: 70px">
                                    <a href="/contact-us" class="dropdown-item">
                                        <span><i class="fas fa-headset"
                                                style="margin-right: 0rem !important; margin-left: 0.5rem !important"></i> {{ __('dashboard.Contact Us') }} </span>
                                    </a>
                                    <span class="dropdown-item">
                                        <span><i class="bi bi-cash"
                                                style="margin-right: 0rem !important; margin-left: 0.5rem !important"></i> {{ __('dashboard.Currency') }}</span>
                                        <span>
                                            <a href="{{ request()->getRequestUri() }}?currency=SAR"
                                                class="{{ session('currency') == 'SAR' ? 'active' : '' }}">SAR</a>
                                            <a href="{{ request()->getRequestUri() }}?currency=USD"
                                                class="{{ session('currency') == 'USD' ? 'active' : '' }}">USD</a>
                                        </span>
                                    </span>
                                    <a href="/sign-in" class="dropdown-item">
                                        <span><i class="bi bi-box-arrow-in-right"
                                                style="margin-right: 0rem !important; margin-left: 0.5rem !important"></i>{{ __('dashboard.sign_in') }}</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </nav>
        </div>
        <!-- Navbar End -->

        @include('website.message')
        @yield('content')

        @include('website.footer')

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-warning btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.9.3/build/js/intlTelInput.min.js"></script>
    <script src="{{ asset('site/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('site/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('site/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('site/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('site/js/star-rating.min.js') }}"></script>
    <script src="{{ asset('site/js/moment.min.js') }}"></script>
    <script src="{{ asset('site/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('site/js/jquery-asRange.min.js') }}"></script>
    <script src="{{ asset('site/js/lightgallery.min.js') }}"></script>
    <script src="{{ asset('site/js/plugins/zoom/lg-zoom.umd.js') }}"></script>
    <script src="{{ asset('site/js/plugins/thumbnail/lg-thumbnail.umd.js') }}"></script>
    <!-- phone -->
    <script>
        const $dynamicimgGallery = jQuery('#dynamic-mode-images');
        const dynamicimgEl = [{
            src: 'site/img/gallery1.png',
            thumb: 'site/img/gallery1.png',
        }];
        const dynamicimgGallery = window.lightGallery($dynamicimgGallery[0], {
            dynamic: true,
            hash: false,
            rotate: false,
            plugins: [
                lgZoom,
                lgThumbnail
            ],
            dynamicEl: dynamicimgEl,
        });
        $dynamicimgGallery.on('click', function() {
            dynamicimgGallery.openGallery(5);
        });

        $('#gallery-dynamic-thumbnails .gallery-item').on('click', function() {
            var index = $(this).index();
            dynamicimgGallery.openGallery(index);
        });

        jQuery('#gallery-dynamic-thumbnails')
            .justifiedGallery({
                captions: false,
                lastRow: 'justify',
                rowHeight: 180,
                margins: 5,
            });
    </script>

    <!-- Template Javascript -->
    <script src="{{ asset('site/js/main.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js">
    <style>
        <style>.dropdown {
            position: relative;
            width: 300px;
        }

        #searchInput {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        #dropdownList {
            position: absolute;
            width: 100%;
            max-height: 200px;
            border: 1px solid #ccc;
            background-color: white;
            overflow-y: auto;
            display: none;
            z-index: 1000;
        }

        #dropdownList option {
            padding: 8px;
            cursor: pointer;
        }
    </style>
    @section('newJs')
    @show
</body>

</html>
