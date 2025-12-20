@extends('errors::illustrated-layout')

@section('title', __('dashboard.forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'dashboard.forbidden'))
@section('image')
<img src="{{ asset('errors/403.png') }}" alt="403">
@endsection