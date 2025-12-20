@extends('admin.layout')
@section('content')
    <x-page-header :header="__('dashboard.operations')"></x-page-header>

    <div class="content">
        <div class="tab-content">
            <div id="numbers" class="tab-pane fade in active ">
                <div class="row">
                    <div class="col-lg-12">
                        <x-statistic-card :statistics="$statistic_in_one_query" :icons="$icons" :links="$links"></x-statistic-card>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
