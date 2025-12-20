@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
    <script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
    <script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
@endsection
@section('content')
    <x-page-header :header="isset($supervisor)
        ? __('dashboard.edit') . ' ' . __('dashboard.supervisor')
        : __('dashboard.add') . ' ' . __('dashboard.supervisor')" :url="'/admin-panel/supervisor'">
        <li>
            <a href="/admin-panel/supervisor">{{ __('dashboard.view_supervisors') }}</a>
        </li>
        <li class="active">
            {{ isset($supervisor) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.supervisor') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($supervisor)
        ? __('dashboard.edit') . ' ' . __('dashboard.supervisor')
        : __('dashboard.add') . ' ' . __('dashboard.supervisor')" :url="isset($supervisor) ? '/admin-panel/supervisor/' . $supervisor->id : '/admin-panel/supervisor'" :object="isset($supervisor) ? $supervisor : null">
        <x-input key="name" :placeholder="__('dashboard.name')" type="text" :object="isset($supervisor) ? $supervisor : null"></x-input>
        <x-input key="email" :placeholder="__('dashboard.email')" type="email" :object="isset($supervisor) ? $supervisor : null"></x-input>
        <x-input key="phone" :placeholder="__('dashboard.phone')" type="text" :object="isset($supervisor) ? $supervisor : null"></x-input>
        <x-select key="group_privilege_id" :placeholder="__('dashboard.privilege_type')" :object="isset($supervisor) ? $supervisor : null">
            @foreach ($permissions as $permission)
                <option value="{{ $permission->id }}"
                    {{ isset($supervisor) && $supervisor->permission_id == $permission->id ? 'selected' : '' }}>
                    {{ app()->getLocale() == 'ar' ? $permission->name_ar : $permission->name_en }}
                </option>
            @endforeach
        </x-select>
        <x-input key="password" :placeholder="__('dashboard.password')" type="password" :object="null"></x-input>
        <x-input key="password_confirmation" :placeholder="__('dashboard.password_confirmation')" type="password" :object="null"></x-input>
    </x-panel-form>
@endsection
