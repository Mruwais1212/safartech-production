@extends('errors::illustrated-layout')

@section('title', __('dashboard.page_expired'))
@section('code', '419')
@section('message', __('dashboard.page_expired'))
@section('image')
<img src="{{ asset('errors/419.png') }}" alt="419">
@endsection