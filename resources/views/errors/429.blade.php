@extends('errors::illustrated-layout')

@section('title', __('dashboard.too_many_requests'))
@section('code', '429')
@section('message', __('dashboard.too_many_requests'))
@section('image')
<img src="{{ asset('errors/429.png') }}" alt="429">
@endsection