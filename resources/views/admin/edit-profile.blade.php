@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.addjs')
@endsection
@section('content')
<x-page-header :header=" __('dashboard.edit').' '.__('dashboard.profile')">
    <li class="active">
        {{ __('dashboard.edit') }} {{ __('dashboard.profile') }}
    </li>
</x-page-header>

<x-panel-form :title="__('dashboard.edit').' '.__('dashboard.profile')"
    url="/admin-panel/edit-profile">
        <x-input key='name' :object="auth('admin')->user()"
        :placeholder="__('dashboard.user_name')" type='text'></x-input>
        <x-input key='email' :object="auth('admin')->user()"
        :placeholder="__('dashboard.email')" type='text'></x-input>
        <x-input key='phone' :object="auth('admin')->user()"
        :placeholder="__('dashboard.phone')" type='text'></x-input>
        <x-input key='password' :object="auth('admin')->user()"
        :placeholder="__('dashboard.password')" type='password'></x-input>
        <x-input key='password_confirmation' :object="auth('admin')->user()"
        :placeholder="__('dashboard.password_confirmation')" type='password'></x-input>
        <x-input-file key='photo' :object="auth('admin')->user()"
        :placeholder="__('dashboard.image')" prefix='/uploads'></x-input-file>
</x-panel-form>
@endsection