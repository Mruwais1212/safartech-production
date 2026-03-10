@extends('website.layout')
@section('title', __('dashboard.about_us'))
@section('content')
    <div style="background-image: url('/site/img/plan.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-12 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.about_us') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="about-page">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="about-section-first">
                        <img src="{{ asset('site/img/plan.jpg') }}" alt="" />
                        <div class="about-section-content">
                            <h3 class="text-warning">
                                {{ app()->getLocale() == 'en' ? $aboutUs->name_en : $aboutUs->name_ar }}
                            </h3>
                            <p>
                                {!! \App\Support\HtmlSanitizer::sanitizeLimitedHtml(app()->getLocale() == 'en' ? $aboutUs->content_en : $aboutUs->content_ar) !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-section-second">
            <h3 class="section-test">
                {{ __('dashboard.The features of Safartech Platform that you will benefit from') }}
            </h3>
            <ul class="section-list">
                <li>
                    <img src="{{ asset('/site/img/about.png') }}" src="" />
                    <span>
                        {{ __('dashboard.High Quality Service') }}
                    </span>
                </li>
                <li>
                    <img src="{{ asset('/site/img/about1.svg') }}" src="" />
                    <span>
                        {{ __('dashboard.Convenient and easy to use') }}
                    </span>
                </li>
                <li>
                    <img src="{{ asset('/site/img/about2.svg') }}" src="" />
                    <span>
                        {{ __('dashboard.Information protection') }}
                    </span>
                </li>
            </ul>
        </div>

        <div class="about-section-third">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ asset('site/img/plan-door.png') }}" alt="">
                    </div>

                    <div class="col-md-6">
                        <h3 class="text-warning">{{ app()->getLocale() == 'en' ? $ourCompanyOverview->name_en : $ourCompanyOverview->name_ar }}</h3>
                        <p>
                            {!! \App\Support\HtmlSanitizer::sanitizeLimitedHtml(app()->getLocale() == 'en' ? $ourCompanyOverview->content_en : $ourCompanyOverview->content_ar) !!}
                        </p>

                        <div class="about-tabs">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                @foreach ($contents as $key => $content)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $key == 0 ? 'active' : '' }}" id="home-tab"
                                            data-bs-toggle="tab" data-bs-target="#home{{ $content->id }}" type="button"
                                            role="tab" aria-controls="home"
                                            aria-selected="true">{{ app()->getLocale() == 'en' ? $content->name_en : $content->name_ar }}</button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="myTabContent">

                                @foreach ($contents as $key => $content)
                                    <div class="tab-pane fade show {{$key == 0 ? 'active' : ''}}" id="home{{ $content->id }}" role="tabpanel"
                                        aria-labelledby="home-tab">
                                        {!! \App\Support\HtmlSanitizer::sanitizeLimitedHtml(app()->getLocale() == 'en' ? $content->content_en : $content->content_ar) !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
