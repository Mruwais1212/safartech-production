@extends('errors::illustrated-layout')

@section('title', __('dashboard.bad_request'))
@section('code', '400')
@section('message', __('dashboard.bad_request'))
@section('image')
<img src="{{ asset('errors/400.png') }}" alt="400">
@endsection