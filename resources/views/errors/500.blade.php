@extends('errors::illustrated-layout')

@section('title', __('dashboard.server_error'))
@section('code', '500')
@section('message', __('dashboard.server_error'))
@section('image')
<img src="{{ asset('errors/500.png') }}" alt="500">
@endsection