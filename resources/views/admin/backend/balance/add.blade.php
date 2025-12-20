@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
@endsection
@section('content')
    <x-page-header :header="isset($balance)
        ? __('dashboard.edit') . ' ' . __('dashboard.balance')
        : __('dashboard.add') . ' ' . __('dashboard.balance')">
        <li><a href="/admin-panel/balance">{{ __('dashboard.view_balances') }}</a></li>
        <li class="active">
            {{ __('dashboard.add') }} {{ __('dashboard.balance') }}
        </li>
    </x-page-header>
    <div style=" text-align:center;margin: 14px;">
        <ul class="nav nav-pills nav-fill" role="tablist">
            <li class="nav-item active">
                <a data-toggle="tab" href="#arabic" class="nav-link">{{ __('dashboard.ar_lang') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#english">{{ __('dashboard.en_lang') }}</a>
            </li>
        </ul>
    </div>
    <x-panel-form :title="isset($balance)
        ? __('dashboard.edit') . ' ' . __('dashboard.balance')
        : __('dashboard.add') . ' ' . __('dashboard.balance')" :url="'/admin-panel/add-balance'" :object="null">
        <div class="tab-content">
            <div id="arabic" class="tab-pane fade in active ">
                <x-textarea key="description_ar" :placeholder="__('dashboard.balance_description_ar')" :object="null"></x-textarea>
            </div>
            <div id="english" class="tab-pane fade ">
                <x-textarea key="description_en" :placeholder="__('dashboard.balance_description_en')" :object="null"></x-textarea>
            </div>
        </div>

        <x-select key='user_id' :placeholder="__('dashboard.select_user')">
            @foreach ($users as $user)
                <option value="{{ $user->id }}"
                    {{ isset($balance) && $balance->user_id == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}</option>
            @endforeach
        </x-select>

        <x-input type="number" key='amount' :object="null" :placeholder="__('dashboard.balance_amount')"></x-input>
    </x-panel-form>
@endsection
