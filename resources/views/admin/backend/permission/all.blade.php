@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.view_group_permissions')" :url="'/admin-panel/permission/create'">
    <li class="active"><a href="">{{ __('dashboard.view_group_permissions') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.view_group_permissions')">
    <thead>
        <tr>
            <th>{{ __('dashboard.id') }} </th>
            <th>{{ __('dashboard.group_name') }}</th>
            <th>{{ __('dashboard.group_permissions') }}</th>
            <th>{{ __('dashboard.action_taken') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($privilege_groups as $key=> $privilege_group)
        <tr parent_id="{{ $privilege_group->id }}">

            <td>{{ $key+1 }}</td>
            <td>{{ app()->getLocale()=='ar'?$privilege_group->name_ar :$privilege_group->name_en }}</td>
            <td>
                @if ($privilege_group->privileges->count())
                @foreach ($privilege_group->privileges as $prev)
                {{ __( app()->getLocale()=='ar'?$prev->name_ar: $prev->name_en) . ' ,' }}
                @endforeach
                @else
                {{ __('dashboard.no_permissions') }}
                @endif
            </td>
            <td align="center" class="center">
                <ul class="icons-list">
                    @if (in_array('permission-delete',$my_permissions))
                    <li class="text-danger-600">
                        <a onclick="return false;" privilege_group_id="{{ $privilege_group->id }}"
                            delete_url="/admin-panel/permission/{{ $privilege_group->id }}" class="sweet_warning"
                            href="#"><i class="icon-trash"></i>
                        </a>
                    </li>
                    @endif
                    @if (in_array('permission-edit',$my_permissions))
                    <li class="text-primary-600">
                        <a href="/admin-panel/permission/{{ $privilege_group->id }}/edit">
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