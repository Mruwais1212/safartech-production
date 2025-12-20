@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header=" __('dashboard.view_fixed_pages')  " :url="'/admin-panel/fixed-page/create'">
    <li class="active">
        <a href="/admin-panel/fixed-page">{{ __('dashboard.view_fixed_pages') }}</a>
    </li>
</x-page-header>
<x-panel-datatable :title="__('dashboard.fixed_pages')">
    <thead>
        <tr>
            <th>{{ __('dashboard.id') }}</th>
            <th>{{ __('dashboard.page_name') }}</th>
            <th>{{ __('dashboard.action_taken') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($fixed_pages as $fixed_page)
        <tr>
            <td>{{ $fixed_page->id }}</td>
            <td>{{ app()->getLocale()=='ar'?$fixed_page->name_ar: $fixed_page->name_en }}</td>
            <td align="center" class="center">
                <ul class="icons-list">
                    @if (in_array('fixed-page-delete',$my_permissions))
                    <li class="text-danger-600">
                        <a onclick="return false;" fixed_page_id="{{ $fixed_page->id }}"
                            delete_url="/admin-panel/fixed-page/{{ $fixed_page->id }}" class="sweet_warning" href="#"><i
                                class="icon-trash"></i>
                        </a>
                    </li>
                    @endif
                    @if (in_array('fixed-page-edit',$my_permissions))
                    <li class="text-primary-600">
                        <a href="/admin-panel/fixed-page/{{ $fixed_page->id }}/edit">
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