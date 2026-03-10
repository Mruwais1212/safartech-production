@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.view_permissions')">
    <li class="active"><a href="">{{ __('dashboard.view_permissions') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.view_permissions')">
    <thead>
        <tr>
            <th>{{ __('dashboard.id') }}</th>
            <th>{{ __('dashboard.privilege_name') }}</th>
            <th>{{ __('dashboard.privilege_icon') }}</th>
            <th>{{ __('dashboard.count_links_under_privilege') }}</th>
            <th>{{ __('dashboard.privilege_status') }}</th>
            <th>{{ __('dashboard.action_taken') }}</th>
        </tr>
    </thead>
    <tbody class="row_position">
        @foreach ($privileges as $privilege)
        <tr parent_id="{{ $privilege->id }}">
            <td>{{ $privilege->id }}</td>
            <td>{{ app()->getLocale()=='ar' ? $privilege->name_ar : $privilege->name_en }}</td>
            <td><i class="{{ $privilege->icon }}"></i></td>
            <td>{{ $privilege->subPrivilege->count() }}</td>
            <td>
                <form method="POST" action="/admin-panel/privilege-change-status/{{ $privilege->id }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0">{{ $privilege->status==1?__('dashboard.hide_privilege'):__('dashboard.show_privilege') }}</button>
                </form>
            </td>
            </a></td>
            <td align="center" class="center">
                <ul class="icons-list">
                    @if (in_array('privileges-edit',$my_permissions))
                    <li class="text-primary-600">
                        <a href="/admin-panel/privilege/{{ $privilege->id }}/edit"><i class="icon-pencil7"></i></a>
                    </li>
                    @endif
                </ul>
            </td>
        </tr>
        @endforeach

    </tbody>
</x-panel-datatable>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $(".row_position").sortable({
            delay: 150,
            stop: function() {
                var selectedData = new Array();
                $('.row_position>tr').each(function() {
                    selectedData.push($(this).attr("parent_id"));
                });
                updateOrder(selectedData);
            }
        });
        function updateOrder(data) {
            console.log(data);
            $.ajax({
                url: "/admin-panel/privilege-change-sort",
                type: 'post',
                data: {
                    position: data
                },
                success: function() {}
            })
        }
</script>
@endsection
