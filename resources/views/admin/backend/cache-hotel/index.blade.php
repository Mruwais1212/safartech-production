@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.ss').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ url('/admin-panel/allhotels') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        _token: "{{ csrf_token() }}"
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "image"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": "address"
                    },
                    {
                        "data": "city_name"
                    },
                    {
                        "data": "country_name"
                    },
                    {
                        "data": "options"
                    }
                ]

            });
        });
    </script>
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_cached_hotels')">
        <li class="active"><a href="">{{ __('dashboard.view_cached_hotels') }}</a></li>
    </x-page-header>
    <div class="content">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title"{{ __('dashboard.view_cached_hotels') }} </h5>
            </div>
            <table class="table ss text-center" data-order='[[ 0, "desc" ]]'>
                <thead>
                    <tr>
                        <th id="sort_id">{{ __('dashboard.id') }}</th>
                        <th>{{ __('dashboard.hotel_name') }}</th>
                        <th>{{ __('dashboard.hotel_image') }}</th>
                        <th>{{ __('dashboard.hotel_phone') }}</th>
                        <th>{{ __('dashboard.hotel_address') }}</th>
                        <th>{{ __('dashboard.city') }}</th>
                        <th>{{ __('dashboard.country') }}</th>
                        <th class="text-center">{{ __('dashboard.action_taken') }}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection
