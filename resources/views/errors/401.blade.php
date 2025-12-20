@extends('errors::illustrated-layout')

@section('title', __('dashboard.unauthorized'))
@section('code', '401')
@section('message', __('dashboard.unauthorized'))
@section('image')
<img src="{{ asset('errors/401.png') }}" alt="401">
@endsection