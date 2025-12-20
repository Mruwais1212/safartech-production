@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
    <script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>
    <script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('[name="type"]').on('change', function() {
                if ($(this).val() == '1') {
                    $('#all-types').css('display', 'block');
                }

                if ($(this).val() == '2') {
                    $('#all-types').css('display', 'none');
                }
            });
        })
        $(document).ready(function() {
    $('.js-example-basic-single').select2();
});
    </script>
@endsection
@section('content')
    <x-page-header :header="isset($group_notification)
        ? __('dashboard.edit') . ' ' . __('dashboard.group_notification')
        : __('dashboard.add') . ' ' . __('dashboard.group_notification')" :url="'/admin-panel/group-notification'">
        <li>
            <a href="/admin-panel/group-notification">{{ __('dashboard.view_group_notifications') }}</a>
        </li>
        <li class="active">
            {{ isset($group_notification) ? __('dashboard.edit') : __('dashboard.add') }}
            {{ __('dashboard.group_notification') }}
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
    <x-panel-form :title="isset($group_notification)
        ? __('dashboard.edit') . ' ' . __('dashboard.group_notification')
        : __('dashboard.add') . ' ' . __('dashboard.group_notification')" :url="isset($group_notification)
        ? '/admin-panel/group-notification/' . $group_notification->id
        : '/admin-panel/group-notification'" :object="isset($group_notification) ? $group_notification : null">
        <div class="tab-content">
            <div id="arabic" class="tab-pane fade in active ">
                <x-input key="title_ar" :placeholder="__('dashboard.group_notification_title_ar')" type="text" :object="isset($group_notification) ? $group_notification : null">></x-input>
                <x-textarea key="message_ar" :placeholder="__('dashboard.group_notification_message_ar')" :object="isset($group_notification) ? $group_notification : null"></x-textarea>
            </div>
            <div id="english" class="tab-pane fade ">
                <x-input key="title_en" :placeholder="__('dashboard.group_notification_title_en')" type="text" :object="isset($group_notification) ? $group_notification : null">></x-input>
                <x-textarea key="message_en" :placeholder="__('dashboard.group_notification_message_en')" :object="isset($group_notification) ? $group_notification : null"></x-textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2">{{ __('dashboard.send_to') }}</label>
            <div class="col-lg-10">
                <input type="radio" name="type" value="1"> {{ __('dashboard.all') }}
            </div>
        </div>
    </x-panel-form>
@endsection
