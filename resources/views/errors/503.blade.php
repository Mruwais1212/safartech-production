@extends('errors::illustrated-layout')

@section('title', __('dashboard.service_unavailable'))
@section('code', '503')
@section('message', __('dashboard.service_unavailable'))
@section('image')
<img src="{{ asset('errors/503.png') }}" alt="503">
@endsection