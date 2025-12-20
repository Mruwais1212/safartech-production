@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header=" __('dashboard.view_page_contents')  " :url="'/admin-panel/page-content/create'">
    <li class="active">
        <a href="/admin-panel/page-content">{{ __('dashboard.view_page_contents') }}</a>
    </li>
</x-page-header>
<x-panel-datatable :title="__('dashboard.page_contents')">
    <thead>
        <tr>
            <th>{{ __('dashboard.id') }}</th>
            <th>{{ __('dashboard.page_name') }}</th>
            <th>{{ __('dashboard.action_taken') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($page_contents as $page_content)
        <tr>
            <td>{{ $page_content->id }}</td>
            <td>{{ app()->getLocale()=='ar'?$page_content->name_ar: $page_content->name_en }}</td>
            <td align="center" class="center">
                <ul class="icons-list">
                    @if (in_array('page-content-delete',$my_permissions))
                    <li class="text-danger-600">
                        <a onclick="return false;" page_content_id="{{ $page_content->id }}"
                            delete_url="/admin-panel/page-content/{{ $page_content->id }}" class="sweet_warning" href="#"><i
                                class="icon-trash"></i>
                        </a>
                    </li>
                    @endif
                    @if (in_array('page-content-edit',$my_permissions))
                    <li class="text-primary-600">
                        <a href="/admin-panel/page-content/{{ $page_content->id }}/edit">
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