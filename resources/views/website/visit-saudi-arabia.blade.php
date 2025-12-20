@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
        <div style="background-image: url('/site/img/trip.png');background-position-y:center ;"
            class="container-fluid header bg-white">

            <div class="background without-waves"></div> <!-- div of shadow and waves -->

            <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
                <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                    <div class="container">
                        <div class="row d-flex align-items-center justify-content-center">
                            <div class="col-md-6 main-header-col">
                                <h1 class="display-5 animated fadeIn mb-4 text-white">Places To Visit</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="steps-form">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="plan-section mt-5">
                                        <div class="plan-gallery">
                                            <div class="container p-0">
                                                <div class="row justify-content-start">
                                                    <div class="col col-md-12 gallery-container-wrap position-relative">
                                                        <button type="button" class="btn btn-dynamic"
                                                            id="dynamic-mode-images">
                                                            <i class="bi bi-images"></i>
                                                            +15
                                                        </button>
                                                        <div class="gallery-container" id="gallery-dynamic-thumbnails">
                                                            <a class="gallery-item" data-index="0"
                                                                data-src="{{ asset('site/img/gallery1.png') }}">
                                                                <img alt="layers of blue." class="img-responsive"
                                                                    src="{{ asset('site/img/gallery1.png') }}" />
                                                            </a>
                                                            <a class="gallery-item" data-index="1"
                                                                data-src="{{ asset('site/img/gallery2.png') }}">
                                                                <img alt="layers of blue." class="img-responsive"
                                                                    src="{{ asset('site/img/gallery2.png') }}" />
                                                            </a>
                                                            <a class="gallery-item" data-index="2"
                                                                data-src="{{ asset('site/img/gallery3.png') }}">
                                                                <img alt="layers of blue." class="img-responsive"
                                                                    src="{{ asset('site/img/gallery3.png') }}" />
                                                            </a>
                                                            <a class="gallery-item" data-index="3"
                                                                data-src="{{ asset('site/img/gallery4.png') }}">
                                                                <img alt="layers of blue." class="img-responsive"
                                                                    src="{{ asset('site/img/gallery4.png') }}" />
                                                            </a>
                                                            <a class="gallery-item" data-index="4"
                                                                data-src="{{ asset('site/img/gallery4.png') }}">
                                                                <img alt="layers of blue." class="img-responsive"
                                                                    src="{{ asset('site/img/gallery4.png') }}" />
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="plan-actions">
                                            <div class="plan-title">
                                                <h1 class="my-2">Dubai, Cruise trip</h1>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="plan-details">
                                            <div class="plan-details-title">
                                                <h6 class="my-2">Traveling details</h6>
                                                <small class="plan-advice text-warning">
                                                    -12°C - Cold
                                                </small>
                                            </div>
                                            <p class="details">
                                                Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac tellus dui
                                                purus. Netus mattis eget in amet diam eu sem et. Sagittis elit egestas
                                                vestibulum sapien vel duis. Sit amet tortor etiam vitae sit etiam velit.
                                                Viverra egestas metus tortor tristique non at sollicitudin cras nunc.
                                                Tellus ut tellus viverra lacus et sed. Quisque augue mauris suscipit
                                                elit tellus. Lorem ipsum dolor sit amet consectetur. Faucibus habitasse
                                                ac tellus dui purus. Netus mattis eget in amet diam eu sem et. Sagittis
                                                elit egestas vestibulum sapien vel duis. Sit amet tortor etiam vitae sit
                                                etiam velit. Viverra egestas metus tortor tristique non at sollicitudin
                                                cras nunc. Tellus ut tellus viverra lacus et sed. Quisque augue mauris
                                                suscipit elit tellus.Lorem ipsum dolor sit amet consectetur. Faucibus
                                                habitasse ac tellus dui purus. Netus mattis eget in amet diam eu sem et.
                                                Sagittis elit egestas vestibulum sapien vel duis. Sit amet tortor etiam
                                                vitae sit etiam velit. Viverra egestas metus tortor tristique non at
                                                sollicitudin cras nunc. Tellus ut tellus viverra lacus et sed. Quisque
                                                augue mauris suscipit elit tellus. Lorem ipsum dolor sit amet
                                                consectetur. Faucibus habitasse ac tellus dui purus. Netus mattis eget
                                                in amet diam eu sem et. Sagittis elit egestas vestibulum sapien vel
                                                duis. Sit amet tort
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection
