@extends('website.layout')
@section('title', __('dashboard.Yalla Plan'))
@section('newCss')
    <style>
        #service {
            text-align: center;
            width: 100%;
            padding: 0 4em;
            position: relative;
        }

        #service #bg-service {
            position: absolute;
            top: 20%;
            right: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            box-sizing: border-box;
            object-fit: cover;
            z-index: -99;
            opacity: 0.2;
        }

        #service h3 {
            font-size: 3rem;
            width: 60%;
            margin: auto auto 0.3em auto;
        }

        #service .text {
            width: 50%;
            margin: auto;
        }

        @media screen and (max-width: 670px) {
            #service h3 {
                font-size: 1.5rem;
                width: 70%;
            }

            #service .text {
                width: 80%;
            }

            #service {
                text-align: center;
                width: 100%;
                padding: 0em;
                position: relative;
            }
        }

        #service .service-container {
            margin-top: 0.5rem;
            color: #000000
        }

        #service .service-container .service-box {
            border-radius: 5px;
            height: 100%;
            padding: 20px;
            background: #fff;
            position: relative;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #service .service-container .service-box p {
            font-size: 17px;
            font-weight: 400;
            width: 84%;
            line-height: 15px;
        }

        #service .service-container .service-box .svg-cont {
            margin-bottom: 0.5em;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #service .service-container .service-box .svg-cont i {
            font-size: 1rem;
            border: 1px solid rgba(128, 128, 128, 0.226);
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.8rem;
            height: 2.8rem;
        }

        /* Loading Spinner Styles */
        .loading-spinner {
            display: none;
            /* Hidden by default */
            text-align: center;
            margin-top: 20px;
        }

        .loading-spinner img {
            width: 50px;
            height: 50px;
        }

        .hidden {
            display: none;
        }
    </style>
@endsection
@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/ai.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">
        <div class="background without-waves"></div>
        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.Yalla Plan') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Steps Form -->
    <div class="container ai-form">
        <div class="row">
            <div class="col-md-12">

                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <section id="service" class="container-fluid my-5 pb-4 wow fadeInUp" data-wow-delay="0.1s">
                                @if (isset($settings_title))
                                    <h3 class="wow fadeInUp" data-wow-delay="0.2s">
                                        {{ $settings_title ? $settings_title->value : '' }}
                                    </h3>
                                @endif
                                @if (isset($settings_content))
                                    <p class="text wow fadeInUp" data-wow-delay="0.5s">
                                        {{ $settings_content ? $settings_content->value : '' }}
                                    </p>
                                @endif
                                @if (count($items) > 0)
                                    <div class="row">
                                        @foreach ($items as $key => $item)
                                            <div class="col-md-3 col-sm-4 col-6 service-container wow fadeInUp"
                                                data-wow-delay="{{ ($key + 1) / 3 }}s">
                                                <div class="service-box">
                                                    <div class="svg-cont">
                                                        <i class='{{ $item->icon }}'></i>
                                                    </div>
                                                    <p>{{ $item['name_' . app()->getLocale()] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </section>
                        </div>
                    </div>
                </div>

                <form id="tripForm" action="/ai-trip-results" class="steps-form" method="post">
                    @csrf
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-10">

                                <div class="text-center mx-auto mb-5 mt-5 wow fadeInUp" data-wow-delay="0.1s"
                                    style="max-width: 100%;">
                                    <h1 class="mb-3 section-title">{{ __('dashboard.Plan Your Dream Trip With AI') }}</h1>
                                </div>
                                <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeInUp"
                                    data-wow-delay="0.2s">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12 mt-4"></div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <!-- Form Fields -->
                                                    <div class="col-md-12">
                                                        <div class="text-start">
                                                            <label class="fw-bold mb-2" for="grid">
                                                                {{ __('dashboard.Where do you want to go') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="select-label"
                                                                for="travelrs">{{ __('dashboard.Your Destination') }}</label>
                                                            <select class="form-control" id="searchableSelectCity"
                                                                name="destination"></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="text-start">
                                                            <label class="fw-bold mb-2" for="grid">
                                                                {{ __('dashboard.When do you want to go') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="startDate">{{ __('dashboard.Start Date') }}</label>
                                                            <div class="div">
                                                                <input type="date" class="form-control date"
                                                                    name="startDate" value=""
                                                                    min="{{ now()->format('Y-m-d') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="endDate">{{ __('dashboard.End Date') }}</label>
                                                            <div class="div">
                                                                <input type="date" class="form-control date"
                                                                    name="endDate" value=""
                                                                    max="{{ now()->addDays(14)->format('Y-m-d') }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mt-3">
                                                        <div class="text-start">
                                                            <label class="fw-bold mb-2" for="grid2">
                                                                {{ __('dashboard.Who is coming with you') }}
                                                            </label>
                                                        </div>
                                                        <div id="grid2" class="grid">
                                                            <label class="card">
                                                                <input name="come_with" class="radio" type="radio"
                                                                    value="Solo">
                                                                <span class="plan-details">
                                                                    <img style="object-fit: contain;" width="auto"
                                                                        src="/site/img/ai2.png" alt="chose type" />
                                                                    <span
                                                                        class="text-center">{{ __('dashboard.Solo') }}</span>
                                                                </span>
                                                            </label>
                                                            <label class="card">
                                                                <input name="come_with" class="radio" type="radio"
                                                                    value="Couple">
                                                                <span class="plan-details">
                                                                    <img style="object-fit: contain;" width="auto"
                                                                        src="/site/img/ai4.png" alt="chose type" />
                                                                    <span
                                                                        class="text-center">{{ __('dashboard.Couple') }}</span>
                                                                </span>
                                                            </label>
                                                            <label class="card">
                                                                <input name="come_with" class="radio" type="radio"
                                                                    value="Family">
                                                                <span class="plan-details">
                                                                    <img style="object-fit: contain;" width="auto"
                                                                        src="/site/img/ai3.png" alt="chose type" />
                                                                    <span
                                                                        class="text-center">{{ __('dashboard.Family') }}</span>
                                                                </span>
                                                            </label>
                                                            <label class="card">
                                                                <input name="come_with" class="radio" type="radio"
                                                                    value="Friends">
                                                                <span class="plan-details">
                                                                    <img style="object-fit: contain;" width="auto"
                                                                        src="/site/img/ai1.png" alt="chose type" />
                                                                    <span
                                                                        class="text-center">{{ __('dashboard.Friends') }}</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="text-start">
                                                            <label class="fw-bold mb-2" for="grid">
                                                                {{ __('dashboard.Which activities are you interested in') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <div class="text-start">
                                                            <label
                                                                for="grid2">{{ __('dashboard.Travel Interests') }}</label>
                                                        </div>
                                                        <div id="grid2" class="grid">
                                                            @foreach ($interests as $interest)
                                                                <label class="card">
                                                                    <input name="interests[]" class="radio travel"
                                                                        type="checkbox"
                                                                        value="{{ $interest->name_en }}" />
                                                                    <span class="plan-details">
                                                                        <img style="object-fit: contain; height: 50px"
                                                                            width="auto"
                                                                            src="{{ is_file('uploads/' . $interest->image) ? asset('uploads/' . $interest->image) : asset('images/placeholder.png') }}"
                                                                            alt="{{ $interest->name_en }}" />
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? $interest->name_ar : $interest->name_en }}</span>
                                                                    </span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="text-start">
                                                                <label class="fw-bold mb-4" for="grid">
                                                                    {{ __('dashboard.What is Your Budget') }}
                                                                </label>
                                                            </div>
                                                            <div id="grid3" class="grid">
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="1500,40000" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? 'منخفضة (0 - 500) ريال' : 'Low (0 - 500) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="500,1000" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? 'متوسطة (500 - 1000) ريال' : 'Medium (0 - 500) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                                <label class="card">
                                                                    <input name="budget" class="radio travel"
                                                                        type="radio" value="1000,40000" />
                                                                    <span class="plan-details">
                                                                        <span class="text-center">
                                                                            {{ app()->getLocale() == 'ar' ? 'مرتفعة (+1000) ريال' : 'High (+1000) SAR' }}</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-8 food-option">
                                                        <div class="big-div">
                                                            <div class="text-start">
                                                                <label
                                                                    class="fw-bold">{{ __('dashboard.Food Option') }}</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    value="Halal" name="food[]">
                                                                <label class="form-check-label"
                                                                    for="exampleCheck1">{{ __('dashboard.Halal') }}</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    value="Vegetarian" name="food[]">
                                                                <label class="form-check-label"
                                                                    for="exampleCheck1">{{ __('dashboard.Vegetarian') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" id="submitBtn"
                                        class="submit-btn btn btn-warning animated fadeIn">
                                        {{ __('dashboard.Show me the plans') }}
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container prepare-page" id="loadingSpinner" style="display: none">
        <div class="row">
            <div class="col-md-12">
                <!-- <form class="steps-form"> -->
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.2s">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div id="wrap">
                                        <div class="ball"></div>
                                        <div class="ball"></div>
                                        <div class="ball"></div>
                                    </div>
                                </div>
                                <h2 class="prepare-title">{{ __('dashboard.Preparing Your Plans') }}</h2>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tripForm').on('submit', function(e) {
                // e.preventDefault();

                var formValid = true;

                if ($('#searchableSelectCity').val() === '' || $('input').val() === '') {
                    formValid = false;
                    alert("Please fill out all required fields!");
                }

                if (formValid) {
                    $('#tripForm').hide();
                    $('#loadingSpinner').show();
                }
            });
        });


        $(document).ready(function() {

            $('#searchableSelectCity').select2({
                placeholder: 'Search for a city at least 3 characters',
                minimumInputLength: 3,
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                ajax: {
                    url: '/search-airport-city',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            city_name: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.city,
                                    text: item.country + ' (' + item.city + ') ',
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endsection
