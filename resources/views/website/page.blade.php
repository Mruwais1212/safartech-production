@extends('website.layout')
@section('title', $page)
@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/plan.jpg');background-position-y:center ;" class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-12 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ $page }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- steps form -->
    <div class="single-page">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-name">
                        <abbr>{{$page}}</abbr>
                    </h3>
                    
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Steps form End -->
@endsection
