@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
@endsection

@section('js_files_footer')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
        integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@section('content')


    <x-page-header :header="__('dashboard.ai_planner_sections')" :url="'/admin-panel/ai-planner-sections/create'">
        <li class="active">
            <a href="/admin-panel/ai-planner-sections">{{ __('dashboard.view_ai_planner_sections') }}</a>
        </li>
    </x-page-header>

    <div class="content">
        
        <div class="panel panel-flat">
            <form method="post" class="form-horizontal" action="/admin-panel/settings?lang={{ app()->getLocale() }}">
                {!! csrf_field() !!}
                <div class="tab-content tabs tabs-style-linebox">
                    <div class="panel-body">
                        @foreach ($settings as $setting)
                            <fieldset class="content-group">
                                <div class="form-group">
                                    <label
                                        class="control-label col-lg-2">{{ app()->getLocale() == 'ar' ? $setting->name_ar : $setting->name_en }}</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}"
                                            class="form-control">
                                        @if ($errors->has('settings[{{ $setting->id }}]'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first("settings[".$setting->id."]") }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </fieldset>
                        @endforeach
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <x-panel-datatable :title="__('dashboard.ai_planner_sections')">
        <thead>
            <tr>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.section_name') }}</th>
                <th>{{ __('dashboard.section_icon') }}</th>
                <th>{{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ app()->getLocale() == 'ar' ? $item->name_ar : $item->name_en }}</td>
                    <td><i class="{!! $item->icon !!}"></td>
                    <td align="center" class="center">
                        <ul class="icons-list">
                            @if (in_array('ai-planner-sections-delete', $my_permissions))
                                <li class="text-danger-600">
                                    <a onclick="return false;" fixed_page_id="{{ $item->id }}"
                                        delete_url="/admin-panel/ai-planner-sections/{{ $item->id }}"
                                        class="sweet_warning" href="#"><i class="icon-trash"></i>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('ai-planner-sections-edit', $my_permissions))
                                <li class="text-primary-600">
                                    <a href="/admin-panel/ai-planner-sections/{{ $item->id }}/edit">
                                        <i class="icon-pencil7"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-panel-datatable>
@endsection
