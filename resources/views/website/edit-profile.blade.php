@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
    <div style="background-image: url('site/img/info-banner.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div>

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-12 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{__('dashboard.Profile')}}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form class="steps-form" action="/edit-profile" method="post">
                @csrf
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s"
                                    style="max-width: 100%;">
                                    <h4 class="mb-3 section-title mt-4">{{__('dashboard.Edit Your Profile')}}</h4>
                                </div>
                                <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeInUp"
                                    data-wow-delay="0.2s">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group text-start">
                                                    <label class="fw-bold mb-3" for="grid">{{__('dashboard.Edit Your Profile')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">{{__('dashboard.name')}}</label>
                                                            <input type="text" name="name" class="form-control" placeholder="{{__('dashboard.name')}}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1">{{__('dashboard.phone')}}</label>
                                                            <input type="number" class="form-control phone" name="phone"
                                                                id="exampleInputEmail1" aria-describedby="emailHelp"
                                                                placeholder="{{__('dashboard.phone')}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-warning py-3 px-5 me-3 mt-4 animated fadeIn w-50">
                                        {{__('dashboard.save_changes')}}
                                    </button>
                                    <a href="/change-password"
                                        class="btn btn-outline-warning py-3 px-5 me-3 mt-4 animated fadeIn w-50">
                                        {{__('dashboard.update_password')}}
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
