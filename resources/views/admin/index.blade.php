@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.chartjs')
    <script>
        $(function() {

            $.ajax({
                url: '/admin-panel/tbo-flight-balance',
                success: function (data) {
                    console.log(data);
                    if (data.status == true) {
                        var msg_count=data.response/0.035;
                        $('#sms_balance').html(parseInt(msg_count)  + ' رسال ');
                        $('#balance_note').html('الرصيد :'+data.response+' ريال وسعر الرسالة 0.035 ريال ');
                    }
                }
            });
        });

    </script>

@endsection
@section('content')
    <x-page-header :header="__('dashboard.home')"></x-page-header>

    <div style=" text-align:center;margin: 10px;">
        <ul class="nav nav-pills nav-fill" role="tablist">
            <li class="nav-item active">
                <a data-toggle="tab" href="#numbers" class="nav-link">{{ __('dashboard.statistics_in_numbers') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#chart">{{ __('dashboard.statistics_in_chart') }}</a>
            </li>
        </ul>
    </div>
    <div class="content">
        <div class="tab-content">
            <div id="numbers" class="tab-pane fade in active ">
                <div class="row">
                    <div class="col-lg-12">
                        <x-statistic-card :statistics="$statistic_in_one_query" :icons="$icons" :links="$links"></x-statistic-card>
                    </div>
                </div>
            </div>
            <div id="chart" class="tab-pane fade ">
                <x-chart-home></x-chart-home>
            </div>
        </div>

    </div>
@endsection
