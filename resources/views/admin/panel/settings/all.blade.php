@extends('admin.layout')
@section('js_files')
<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
<script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
<script type="text/javascript" src="/assets/js/plugins/forms/styling/switch.min.js"></script>
<script type="text/javascript" src="/assets/js/plugins/forms/tags/tagsinput.min.js"></script>
<script type="text/javascript" src="/assets/js/pages/form_checkboxes_radios.js"></script>
<script>
    function resetColor(element) {
            $(element).val('#000');
        }
</script>
<style>
    .radio-inline,
    .checkbox-inline {
        padding-right: 0px;
        margin-left: 75px;

    }
</style>
@endsection
@section('content')
<x-page-header :header="__('dashboard.settings')">
    <li class="active"><a href=""> {{ __('dashboard.settings') }}</a></li>
</x-page-header>
<div class="content">
    <div style=" text-align: center;" class="">
        <ul class="nav nav-pills nav-fill " role="tablist">
            @foreach ($setting_types as $key=> $settingType)
            <li class="nav-item {{ $key==0?" active":'' }}"><a data-toggle="tab"
                    href="#setting_content{{ $settingType->id }}" class="nav-link">{{app()->getLocale() == 'ar' ?
                    $settingType->name_ar:$settingType->name_en }}</a></li>
            @endforeach
        </ul>
    </div>
    <form enctype="multipart/form-data" method="post" class="form-horizontal" action="/admin-panel/settings?lang={{ app()->getLocale() }}">
        {!! csrf_field() !!}
        <div class="tab-content tabs tabs-style-linebox">
            @foreach ($setting_types as $key=> $settingType)
            <div id="setting_content{{ $settingType->id }}" class="tab-pane {{ $key==0?'active':'' }}">
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">{{app()->getLocale() == 'ar' ?
                            $settingType->name_ar:$settingType->name_en }}</h5>
                    </div>
                    <div class="panel-body">
                        @foreach ($settings->where('setting_type_id',$settingType->id)->all() as $setting)
                        <fieldset class="content-group">
                            @if ($setting->input_type == 'radio')
                            <div class="form-group">
                                <label class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ?
                                    $setting->name_ar:$setting->name_en }} </label>
                                <div class="col-lg-10">
                                    <?php $old_user_type = old('user_type_id'); ?>
                                    <label class="radio-inline">
                                        <input type="radio" value="0" name="settings[{{ $setting->id }}]" {{
                                            $setting->value
                                        == 0 ? 'checked="checked"' : '' }}>
                                        لا
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" value="1" name="settings[{{ $setting->id }}]" {{
                                            $setting->value
                                        == 1 ? 'checked="checked"' : '' }}>
                                        نعم
                                    </label>
                                    <span class="help-block">
                                        <strong>{{ app()->getLocale()=='ar'?$setting->note_ar:$setting->note_en
                                            }}</strong>
                                    </span>
                                </div>
                            </div>
                            @elseif ($setting->input_type == 'color')
                            <div class="form-group">
                                <label class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ?
                                    $setting->name_ar:$setting->name_en }}</label>
                                <div class="{{ $setting->input_type == 'color' ? 'col-lg-8' : 'col-lg-10' }}">
                                    <input type="{{ $setting->input_type == 'color' ? 'color' : 'text' }}"
                                        name="settings[{{ $setting->id }}]" value="{{ $setting->value }}"
                                        class="form-control option{{ $setting->id }}">
                                    <span class="help-block">
                                        <strong>{{ app()->getLocale()=='ar'?$setting->note_ar:$setting->note_en
                                            }}</strong>
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <a onclick="resetColor('.option{{ $setting->id }}')"><i style="font-size: 36px;"
                                            class="fa fa-refresh"></i></a>
                                </div>
                            </div>
                            @elseif ($setting->input_type == 'number')
                            <div class="form-group">
                                <label class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ?
                                    $setting->name_ar:$setting->name_en }}</label>
                                <div class="col-lg-10">
                                    <input type="number" name="settings[{{ $setting->id }}]"
                                        value="{{ $setting->value }}" class="form-control border-radius">
                                    <span class="help-block">
                                        <strong>{{ app()->getLocale()=='ar'?$setting->note_ar:$setting->note_en
                                            }}</strong>
                                    </span>
                                </div>
                            </div>
                            @elseif ($setting->input_type == 'file')
                            <div class="form-group">
                                <label class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ?
                                    $setting->name_ar:$setting->name_en }}</label>
                                <div class="col-lg-5">
                                    <input type="file" name="settings[{{ $setting->id }}]" class="form-control">
                                    <span class="help-block">
                                        <strong>{{ app()->getLocale()=='ar'?$setting->note_ar:$setting->note_en
                                            }}</strong>
                                    </span>
                                </div>
                                @if (!empty($setting->value))
                                <div class="col-lg-5">
                                    <img height="100px" src="/settings/{{ $setting->value }}" alt="" />
                                    <span class="help-block">
                                        <strong>{{ __('dashboard.image') }}</strong>
                                    </span>
                                </div>
                                @endif
                            </div>
                            @elseif ($setting->input_type == 'textarea')
                            <div class="form-group">
                                <label class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ?
                                    $setting->name_ar:$setting->name_en }}</label>
                                <div class="col-lg-10">
                                    <textarea name="settings[{{ $setting->id }}]"
                                        class="form-control">{{ $setting->value }}</textarea>
                                    <span class="help-block">
                                        <strong>{{ app()->getLocale()=='ar'?$setting->note_ar:$setting->note_en
                                            }}</strong>
                                    </span>
                                </div>
                            </div>
                            @elseif ($setting->input_type == 'tag')
                            <div class="form-group">
                                <label class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ?
                                    $setting->name_ar:$setting->name_en }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}"
                                        class="form-control border-radius" data-role="tagsinput">
                                    <span class="help-block">
                                        <strong>{{ app()->getLocale()=='ar'?$setting->note_ar:$setting->note_en
                                            }}</strong>
                                    </span>
                                </div>
                            </div>
                            @elseif ($setting->input_type == 'url')
                            <div class="form-group">
                                <label class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ?
                                    $setting->name_ar:$setting->name_en }}</label>
                                <div class="col-lg-10">
                                    <input type="url" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}"
                                        class="form-control border-radius" style="direction: ltr;">
                                    <span class="help-block">
                                        <strong>{{ app()->getLocale()=='ar'?$setting->note_ar:$setting->note_en
                                            }}</strong>
                                    </span>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ?
                                    $setting->name_ar:$setting->name_en }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}"
                                        class="form-control">
                                    <span class="help-block">
                                        <strong>{{ app()->getLocale()=='ar'?$setting->note_ar:$setting->note_en
                                            }}</strong>
                                    </span>
                                </div>
                            </div>
                            @endif
                        </fieldset>
                        @endforeach
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.edit') }} {{
                                __('dashboard.settings') }}</button>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </form>

</div>
@endsection