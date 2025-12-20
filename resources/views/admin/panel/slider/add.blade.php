@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
@endsection
@section('content')
    <x-page-header :header="isset($slider)
        ? __('dashboard.edit') . ' ' . __('dashboard.sliders')
        : __('dashboard.add') . ' ' . __('dashboard.sliders')" url="/admin-panel/slider">
        <li>
            <a href="/admin-panel/slider">{{ __('dashboard.view_sliders') }}</a>
        </li>
        <li class="active">{{ isset($slider) ? __('dashboard.edit') : __('dashboard.add') }}
            {{ __('dashboard.slider') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($slider)
        ? __('dashboard.edit') . ' ' . __('dashboard.sliders')
        : __('dashboard.add') . ' ' . __('dashboard.sliders')" :url="isset($slider) ? '/admin-panel/slider/' . $slider->id : '/admin-panel/slider'" :object="isset($slider) ? $slider : null">

        <x-input key='title_ar' :object="isset($slider) ? $slider : null" :placeholder="__('dashboard.slider_title_ar')" type='text'>
        </x-input>

        <x-input key='title_en' :object="isset($slider) ? $slider : null" :placeholder="__('dashboard.slider_title_en')" type='text'>
        </x-input>

        <x-textarea key='description_ar' :object="isset($slider) ? $slider : null" :placeholder="__('dashboard.slider_description_ar')"></x-textarea>

        <x-textarea key='description_en' :object="isset($slider) ? $slider : null" :placeholder="__('dashboard.slider_description_en')"></x-textarea>

        {{-- <x-input key='button_text_ar' :object="isset($slider)?$slider:null" :placeholder="__('dashboard.button_text_ar')"
        type='text'>
    </x-input>

    <x-input key='button_text_en' :object="isset($slider)?$slider:null" :placeholder="__('dashboard.button_text_en')"
        type='text'>
    </x-input>

    <x-input key='button_url' :object="isset($slider)?$slider:null" :placeholder="__('dashboard.button_link')"
        type='url'>
    </x-input> --}}

        <input type="hidden" name="main_slider_id" value="1" id="main_slider_id">
        {{-- <x-select key='main_slider_id' :placeholder="__('dashboard.choose_type_of_slider')">
        <option value="">{{ __('dashboard.choose_type_of_slider') }}</option>
        @foreach ($mainSliders as $mainSlider)
        <option value="{{ $mainSlider->id }}" {{ isset($slider) && $slider->main_slider_id
            == $mainSlider->id ? 'selected' : (old('main_slider_id') == $mainSlider->id ||
            $mainSlider->id == $mainSlider->id ? 'selected' : '') }}>
            {{ $mainSlider->name_ar }}</option>
        @endforeach
    </x-select> --}}

        {{-- <x-input key='text_color' :object="isset($slider) ? $slider : null" :placeholder="__('dashboard.text_color')" type='color'>
        </x-input>
 --}}
        <x-input-file key='photo' :object="isset($slider) ? $slider : null" :placeholder="__('dashboard.slider_image')" :prefix="'/uploads'">
        </x-input-file>
    </x-panel-form>
    <script>
        $(function() {
            $('#has_link').on('change', function(e) {
                const has_link = $('#has_link').is(':checked');
                if (has_link) {
                    $('#item-content').show();
                } else {
                    $('#item-content').hide();
                }
            });
        });
    </script>
@endsection
