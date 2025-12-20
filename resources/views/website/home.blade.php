@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
    <!-- Header Start -->
    <div class="container-fluid header bg-white">

        <div class="background"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            @if (app()->getLocale() == 'ar')
                                <h1 class="display-5 animated fadeIn mb-4 text-white">أطلق العنان لشغفك بالسفر</h1>
                                <p class="animated fadeIn mb-4 pb-2 text-white"> اكتشف العالم مع سفرتك مغامرتك في انتظارك</p>
                                <a href="/ai-trip" class="btn btn-warning py-3 px-5 me-3 animated fadeIn">خطط لرحلتك
                                    الآن</a>
                            @else
                                <h1 class="display-7 animated fadeIn mb-4 text-white">Unleash your passion for travel</h1>
                                <p class="animated fadeIn mb-4 pb-2 text-white">Explore the World with SafarTech Your
                                    Adventure Awaits </p>
                                <a href="/ai-trip" class="btn btn-warning py-3 px-5 me-3 animated fadeIn">Plan A
                                    Trip Now</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 animated fadeIn">
                <div class="container">
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-6">
                            <div class="owl-carousel header-carousel">

                                @foreach ($page_contents as $page_content)
                                    <div class="owl-carousel-item">
                                        <h3>
                                            {{ app()->getLocale() == 'ar' ? $page_content->name_ar : $page_content->name_en }}
                                        </h3>
                                        <p> {{ app()->getLocale() == 'ar' ? $page_content->content_ar : $page_content->content_en }}
                                        </p>
                                    </div>
                                @endforeach

                                {{-- <div class="owl-carousel-item">
                                    @if (app()->getLocale() == 'ar')
                                        <h3>كن مستعد لرحلة مليئة بالمتعة و الراحة</h3>
                                        <p>احجز رحلة أحلامك مع منصة سفرتك سيمكنك إدارة رحلتك كاملة و مقارنة الأسعار و حجز
                                            رحلات الطيران و الإقامة من واجهة واحدة فقط</p>
                                    @else
                                        <h3>Seamless Booking and Management</h3>
                                        <p>
                                            Hassle-free travel starts with SafarTech. Compare prices, book flights, and
                                            accommodation, and manage your entire trip from a single, user-friendly
                                            interface.
                                        </p>
                                    @endif
                                </div>

                                <div class="owl-carousel-item">
                                    @if (app()->getLocale() == 'ar')
                                        <h3>رحلة أحلامك سلسلة و سهلة و بدون عناء</h3>
                                        <p>
                                            في سفرتك نوظف الذكاء الاصطناعي ليتيح لك خيارات لا محدودة من الوجهات العالمية
                                            لتستمتع بأسلوب سفر يتلائم مع تفضيلاتك و ميزانيتك
                                        </p>
                                    @else
                                        <h3>Let Us Take the Guesswork Out</h3>
                                        <p>
                                            Ditch the stress of planning and let our AI-powered algorithm take care of the
                                            details. We'll find the best deals, book your flights and accommodations, and
                                            create an itinerary that will make your next adventure unforgettable.
                                        </p>
                                    @endif
                                </div> --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Property List Start -->
    <div class="container py-5">
        <div class="row g-4">
            <div class="text-start mx-auto wow fadeInUp p-0" data-wow-delay="0.1s"
                style="max-width: 100%; visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
                <h1 class="mb-1 section-title pb-2 lined-title aftrer-title">{{ __('dashboard.trips') }}</h1>
            </div>
            @foreach ($trips as $trip)
                <div class="col-xxl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="property-item overflow-hidden" style="position: relative;">
                        <div class="bg-white text-primary d-flex justify-content-between align-items-center py-3 px-3">
                            <p class="hotel-country m-0">
                                {{ app()->getLocale() == 'ar' ? $trip->country->name_ar : $trip->country->name_en }} ,
                                {{ app()->getLocale() == 'ar' ? $trip->city->name_ar : $trip->city->name_en }}
                            </p>
                        </div>
                        <div class="position-relative overflow-hidden">
                            <a href="/trips/{{ $trip->id }}">
                                <img class="img-fluid"
                                    src="{{ is_file('uploads/' . $trip->image) ? asset('uploads/' . $trip->image) : '/site/img/property-1.jpg' }}"alt="">
                            </a>
                        </div>
                        <div class="px-4 py-2 pb-0">
                            <div class="hotel-title text-warning mb-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center group-travel-interests justify-content-start">
                                    @foreach ($trip->travelInterests as $interest)
                                        <p class="travel-interests">
                                            {{ app()->getLocale() == 'ar' ? $interest->name_ar : $interest->name_en }}</p>
                                    @endforeach
                                </div>
                            </div>
                            <p class="hotel-desc">
                                {!! app()->getLocale() == 'ar' ? $trip->description_ar : $trip->description_en !!}
                            </p>
                        </div>
                        <div class="px-4 py-2 d-flex justify-content-between align-items-center border-top hotel-bottom" style="position: absolute; bottom: 0; width: 100%;">
                            {{-- <small class="py-2">
                                {{ __('dashboard.Starting from') }} :
                                <br>
                                <h5>{{ $trip->price }} {{ $trip->currency }}</h5>
                            </small> --}}
                            <small class="py-2 w-100">
                                <a href="/trips/{{ $trip->id }}"
                                    class="btn btn-warning py-2 w-100 px-3">{{ __('dashboard.view_details') }}</a>
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($trips->count() > 8)
                <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s">
                    <a class="btn btn-warning py-3 px-5" href="/trips">{{ __('dashboard.more_trips') }}</a>
                </div>
            @endif
        </div>
    </div>
    <!-- Property List End -->
@endsection
