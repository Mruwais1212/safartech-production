@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
        integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/css/fontawesome-iconpicker.min.css"
        rel="stylesheet">
    <script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
    <script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
@endsection
@section('js_files_footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/js/fontawesome-iconpicker.js"></script>
    <script>
        $('.icp-auto').iconpicker();
    </script>
@endsection
@section('content')
    <x-page-header :header="isset($item)
        ? __('dashboard.edit') . ' ' . __('dashboard.ai_planner_sections')
        : __('dashboard.add') . ' ' . __('dashboard.ai_planner_sections')" :url="'/admin-panel/ai-planner-sections'">
        <li>
            <a href="/admin-panel/ai-planner-sections">{{ __('dashboard.view_ai_planner_sections') }}</a>
        </li>
        <li class="active">
            {{ isset($item) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.ai_planner_sections') }}
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
    <x-panel-form :title="isset($item)
        ? __('dashboard.edit') . ' ' . __('dashboard.ai_planner_sections')
        : __('dashboard.add') . ' ' . __('dashboard.ai_planner_sections')" :url="isset($item) ? '/admin-panel/ai-planner-sections/' . $item->id : '/admin-panel/ai-planner-sections'" :object="isset($item) ? $item : null">
        <div class="tab-content">
            <div id="arabic" class="tab-pane fade in active ">
                <x-input key="name_ar" :placeholder="__('dashboard.item_name_ar')" type="text" :object="isset($item) ? $item : null">></x-input>
            </div>
            <div id="english" class="tab-pane fade ">
                <x-input key="name_en" :placeholder="__('dashboard.item_name_en')" type="text" :object="isset($item) ? $item : null">></x-input>
            </div>
            <x-input key="sort" :placeholder="__('dashboard.item_sort')" type="number" min="0" :object="isset($item) ? $item : null">></x-input>

            <div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
                <label class="control-label col-lg-2">{{ __('dashboard.icon') }}</label>
                <div class="col-lg-10">
                    <input class="form-control icp icp-auto" name="icon" value="{{ isset($item) ? $item->icon : 'fas fa-anchor' }}" type="text" />
                    @if ($errors->has('icon'))
                    <span class="help-block">
                        <strong>{{ $errors->first('icon') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </x-panel-form>
@endsection
