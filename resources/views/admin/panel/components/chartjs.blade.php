    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            var saturday = {{ json_encode($statistic_in_day['saturday']) }};
            var sunday = {{ json_encode($statistic_in_day['sunday']) }};
            var monday = {{ json_encode($statistic_in_day['monday']) }};
            var tuesday = {{ json_encode($statistic_in_day['tuesday']) }};
            var wednesday = {{ json_encode($statistic_in_day['wednesday']) }};
            var thursday = {{ json_encode($statistic_in_day['thursday']) }};
            var friday = {{ json_encode($statistic_in_day['friday']) }};

            const data_days = {
                labels: [
                    'Saturday',
                    'Sunday',
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday'
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [saturday, sunday, monday, tuesday, wednesday, thursday, friday],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 85)',
                        'rgb(265, 69, 14)',
                        'rgb(84, 72, 35)',
                        'rgb(25, 25, 86)',
                        'rgb(255, 255, 0)',
                        'rgb(200, 72, 235)',
                    ],
                    hoverOffset: 4
                }]
            };

            const config_days = {
                type: 'pie',
                data: data_days,
            };
            const myChart_days = new Chart(
                document.getElementById('myChart-days'),
                config_days
            );

            var term1 = {{ json_encode($statistic_in_term['term1']) }};
            var term2 = {{ json_encode($statistic_in_term['term2']) }};
            var term3 = {{ json_encode($statistic_in_term['term3']) }};
            var term4 = {{ json_encode($statistic_in_term['term4']) }};

            const data_term = {
                labels: [
                    'First Quarter',
                    'Second Quarter',
                    'Third Quarter',
                    'Fourth Quarter'
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [term1, term2, term3, term4],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 85)',
                        'rgb(265, 69, 14)',
                        'rgb(25, 25, 86)',
                    ],
                    hoverOffset: 4
                }]
            };

            const config_term = {
                type: 'pie',
                data: data_term,
            };
            const myChart_term = new Chart(
                document.getElementById('myChart-term'),
                config_term
            );


            const data_month = {
                labels: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ],
                datasets: [{
                    type: 'bar',
                    label: 'Bar',
                    data: {{ json_encode(array_values($statistic_in_month)) }},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)'
                }, {
                    type: 'line',
                    label: 'Line',
                    data: {{ json_encode(array_values($statistic_in_month)) }},
                    fill: false,
                    borderColor: 'rgb(54, 162, 235)'
                }]
            };

            const config_month = {
                type: 'scatter',
                data: data_month,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
            const myChart_month = new Chart(
                document.getElementById('myChart-month'),
                config_month
            );

            const data_hotel_month = {
                labels: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ],
                datasets: [{
                    type: 'bar',
                    label: 'Bar',
                    data: {{ json_encode(array_values($hotel_reservations_in_month)) }},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)'
                }, {
                    type: 'line',
                    label: 'Line',
                    data: {{ json_encode(array_values($hotel_reservations_in_month)) }},
                    fill: false,
                    borderColor: 'rgb(54, 162, 235)'
                }]
            };

            const config_hotel_month = {
                type: 'scatter',
                data: data_hotel_month,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            const myChart_hotel_month = new Chart(
                document.getElementById('myChart-hotel-month'),
                config_hotel_month
            );

            const data_flight_month = {
                labels: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ],
                datasets: [{
                    type: 'bar',
                    label: 'Bar',
                    data: {{ json_encode(array_values($flight_reservations_in_month)) }},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)'
                }, {
                    type: 'line',
                    label: 'Line',
                    data: {{ json_encode(array_values($flight_reservations_in_month)) }},
                    fill: false,
                    borderColor: 'rgb(54, 162, 235)'
                }]
            };

            const config_flight_month = {
                type: 'scatter',
                data: data_flight_month,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            const myChart_flight_month = new Chart(
                document.getElementById('myChart-flight-month'),
                config_flight_month
            );

            const hoursOfDay = {
                labels: ['00:00-03:59', '04:00-07:59', '08:00-11:59', '12:00-15:59', '16:00-19:59',
                    '20:00-23:59'
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: {{ json_encode(array_values($statistic_in_hours)) }},
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 85)',
                        'rgb(265, 69, 14)',
                        'rgb(84, 72, 35)',
                        'rgb(25, 25, 86)',
                        'rgb(255, 255, 0)',
                    ],
                    hoverOffset: 4
                }]
            };

            const hoursOfDayConfig = {
                type: 'pie',
                data: hoursOfDay,
            };
            const chartInHours = new Chart(
                document.getElementById('chartInHours'),
                hoursOfDayConfig
            );
        });
    </script>
