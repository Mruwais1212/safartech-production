@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
@endsection
@section('content')
    <x-page-header :header="isset($hotel)
        ? __('dashboard.edit') . ' ' . __('dashboard.cached_hotels')
        : __('dashboard.add') . ' ' . __('dashboard.cached_hotels')" :url="'/admin-panel/cached-hotels'">
        <li><a href="/admin-panel/cached-hotels">{{ __('dashboard.view_cached_hotels') }}</a></li>
        <li class="active">
            {{ isset($hotel) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.cached_hotels') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($hotel)
        ? __('dashboard.edit') . ' ' . __('dashboard.cached_hotels')
        : __('dashboard.add') . ' ' . __('dashboard.cached_hotels')" :url="isset($hotel) ? '/admin-panel/cached-hotels/' . $hotel->id : '/admin-panel/cached-hotels'" :object="isset($hotel) ? $hotel : null">
        <x-input key='name_ar' :object="isset($hotel) ? $hotel : null" :placeholder="__('dashboard.name_ar')" type='text'></x-input>

        <x-input key='name_en' :object="isset($hotel) ? $hotel : null" :placeholder="__('dashboard.name_en')" type='text'></x-input>

        <x-input-files key='images' perfix="" options='multiple' :object="isset($hotel) ? $hotel : null" :placeholder="__('dashboard.hotel_images')" type='file'></x-input-files>


        </fieldset>
    </x-panel-form>
    <hr>
    {{ __('dashboard.hotel_images') }}
    @foreach (json_decode($hotel->images) as $image)
        <div style="display: inline-block;margin:0px 1px 4px 1px">
            <img src="{{ $image ? : url('/no-image.png') }}"
                style="width: 100px; height: 100px;">
            <br>
            <form action="{{ url('admin-panel/delete-image-cached-hotels/'.$hotel->id) }}" method="POST" style="width:100%">
                @csrf
                <input type="hidden" value="{{ $image }}" name="image_url"/>
                <button style="width:100%" type="submit" class="btn btn-danger">{{ __('dashboard.delete') }}</button>
            </form>
        </div>
    @endforeach
@endsection
