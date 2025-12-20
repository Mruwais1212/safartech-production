@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $('#select_travel_interests').select2();

            $('#btn_search').click(function() {
                select_country_id = $('#select_country_id').val();
                travel_interests = $('#select_travel_interests').val();
                if (select_country_id == '' || travel_interests == '') {
                    alert('{{ __('dashboard.select_country_and_travel_interests') }}');
                }

                $('#table_data').html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-top: 60px; margin-bottom: 60px"></i>');
                ajax = $.ajax({
                    url: '/admin-panel/cache-destination',
                    type: 'post',
                    data: {
                        country_id: select_country_id,
                        travel_interests: travel_interests
                    },
                    success: function(response) {
                        $('#data_table').html();
                        data = response.data;
                        console.log(data);
                        table =
                            '<table class="table table-bordered table-striped text-center">';
                        table += '<thead>';
                        table += '<tr>';
                        table += '<th>{{ __('dashboard.description') }}</th>';
                        table += '<th>{{ __('dashboard.country') }}</th>';
                        table += '<th>{{ __('dashboard.city') }}</th>';
                        table += '<th>{{ __('dashboard.estimated_cost') }}</th>';
                        table += '</tr>';
                        table += '</thead>';
                        table += '<tbody>';
                        data.forEach(element => {
                            table += '<tr>';
                            table += '<td>' + element.description + '</td>';
                            table += '<td>' + element.country.name + '</td>';
                            table += '<td>' + element.city.name + '</td>';
                            table += '<td>' + element.estimated_cost +'  {{ __('dashboard.dollar') }} </td>';
                            table += '</tr>';
                        })
                        table += '</tbody>';
                        table += '</table>';
                        $('#table_data').html(table);
                    },
                    error: function(response) {
                        $('#data_table').html();
                        $('#table_data').html('<h1>{{ __('dashboard.no_data') }}</h1>');
                        console.log(response);
                    }
                })
            })
        });
    </script>
@endsection
@section('content')
    <x-page-header :header="__('dashboard.cache_destinations')">
        <li class="active"><a href="">{{ __('dashboard.cache_destinations') }}</a></li>
    </x-page-header>

    <div style=" text-align:center;margin: 10px; border: 1px solid #ddd; padding: 10px; background-color: #fff">
        <select id="select_country_id" class="form-control" name="country_id" style="margin-bottom: 10px">
            <option value="">{{ __('dashboard.select_country') }}</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}">
                    {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}</option>
            @endforeach
        </select>

        <select class="form-control select2" id="select_travel_interests" name="travel_interests[]"
            style="margin-bottom: 10px" multiple>
            <option value="">{{ __('dashboard.select_travel_interests') }}</option>
            @foreach ($travelInterests as $travelInterest)
                <option value="{{ $travelInterest->id }}">
                    {{ app()->getLocale() == 'ar' ? $travelInterest->name_ar : $travelInterest->name_en }}
                </option>
            @endforeach
        </select>

        <button id="btn_search" class="btn btn-primary">{{ __('dashboard.search') }}</button>
    </div>

    <div id="table_data" class="container panel text-center"> </div>
@endsection
