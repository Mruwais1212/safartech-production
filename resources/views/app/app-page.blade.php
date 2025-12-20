@extends('app.layout-app')
@section('title', $title)
@section('content')
    <section class="about-page about">
        <div class="container" style="font-size: 16px;">
            <div class="row align-items-center">
                <div class="{{ 'col-lg-12 col-md-12' }}">
                    <div class="story-wrap explore-content">
                        {!! $data !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
