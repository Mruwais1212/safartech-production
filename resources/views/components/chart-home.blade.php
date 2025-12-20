<div class="chart row">
    <div class="row">
        <div class=" col-lg-4">
            <div class="box-body">
                <p class="text-center">{{ __('dashboard.count_reservation_in_days_of_the_week') }}</p>
                <canvas id="myChart-days"></canvas>
            </div>
        </div>

        <div class=" col-lg-4">
            <div class="box-body">
                <p class="text-center">{{ __('dashboard.count_reservation_in_terms_of_the_year') }}</p>
                <canvas id="myChart-term"></canvas>
            </div>
        </div>

        <div class=" col-lg-4">
            <div class="box-body">
                <p class="text-center">{{ __('dashboard.count_reservation_in_hours_of_the_day') }}</p>
                <canvas id="chartInHours"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class=" col-lg-6">
            <div class="box-body">
                <p class="text-center">{{ __('dashboard.count_reservation_in_month') }}</p>
                <canvas id="myChart-month"></canvas>
            </div>
        </div>

        <div class=" col-lg-6">
            <div class="box-body">
                <p class="text-center">{{ __('dashboard.count_flight_reservation_in_month') }}</p>
                <canvas id="myChart-hotel-month"></canvas>
            </div>
        </div>

        <div class=" col-lg-6">
            <div class="box-body">
                <p class="text-center">{{ __('dashboard.count_flight_reservation_in_month') }}</p>
                <canvas id="myChart-flight-month"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    canvas {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
    }
</style>
