@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
    <script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
    <script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
@endsection
@section('content')
    <x-page-header :header="isset($user)
        ? __('dashboard.edit') . ' ' . __('dashboard.user')
        : __('dashboard.add') . ' ' . __('dashboard.user')" :url="'/admin-panel/users'">
        <li>
            <a href="/admin-panel/users">{{ __('dashboard.view_users') }}</a>
        </li>
        <li class="active">
            {{ isset($user) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.user') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($user)
        ? __('dashboard.edit') . ' ' . __('dashboard.user')
        : __('dashboard.add') . ' ' . __('dashboard.user')" :url="isset($user) ? '/admin-panel/users/' . $user->id : '/admin-panel/users'" :object="isset($user) ? $user : null">
        <x-input key="name" :placeholder="__('dashboard.name')" type="text" :object="isset($user) ? $user : null"></x-input>
        <x-input key="email" :placeholder="__('dashboard.email')" type="email" :object="isset($user) ? $user : null"></x-input>
        <x-input key="phone_code" :placeholder="__('dashboard.phone_code')" type="text" :object="isset($user) ? $user : null"></x-input>
        <x-input key="phone" :placeholder="__('dashboard.phone')" type="text" :object="isset($user) ? $user : null"></x-input>
        <x-input-file key='photo' :object="isset($user) ? $user : null" :placeholder="__('dashboard.image')" prefix='/uploads'></x-input-file>
    </x-panel-form>
@endsection
