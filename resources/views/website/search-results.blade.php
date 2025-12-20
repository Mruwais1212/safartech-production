@extends('website.layout')
@section('title', __('dashboard.AI Trip Planner'))

@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/trip.png');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div>

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.AI Trip Planner') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- steps form -->

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form class="steps-form">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="progress-container text-center">
                                    <div class="progress" id="progress" style="width:0%;"></div>
                                    <div class="text-wrap active">
                                        <div class="circle">01</div>
                                        <p class="text">{{ __('dashboard.Plan') }}</p>
                                    </div>
                                    <div class="text-wrap" id="second-step">
                                        <div class="circle">02</div>
                                        <p class="text">{{ __('dashboard.Compare') }}</p>
                                    </div>
                                    <div class="text-wrap">
                                        <div class="circle">03</div>
                                        <p class="text">{{ __('dashboard.Select') }}</p>
                                    </div>
                                    <div class="text-wrap">
                                        <div class="circle">04</div>
                                        <p class="text">{{ __('dashboard.Payment') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container prepare-page" style="display: none">
        <div class="row">
            <div class="col-md-12">
                <!-- <form class="steps-form"> -->
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="form-inputs bg-grey text-center mx-auto mb-5 wow fadeIn" data-wow-delay="0.2s">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div id="wrap">
                                        <div class="ball"></div>
                                        <div class="ball"></div>
                                        <div class="ball"></div>
                                    </div>
                                </div>
                                <h2 class="prepare-title">{{ __('dashboard.Preparing Your Plans') }}</h2>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>

    <div class="container plans-page" style="display: none">
        <div class="row">
            <div class="col-md-12">
                <div class="container">
                    <div class="row justify-content-center">
                        <!-- Property List Start -->
                        <div class="container py-5">
                            <div class="container">
                                <div class="row g-4">
                                    <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s"
                                        style="max-width: 100%;">
                                        <h1 class="mb-1 section-title pb-2 lined-title">
                                            {{ __('dashboard.Suggestions Plans') }}</h1>
                                    </div>
                                    <div id="plans-container" class="row"></div>
                                    <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s"
                                        style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
                                        <a class="btn btn-warning py-3 px-5"
                                            href="/ai-trip">{{ __('dashboard.Generate A new plan') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Property List End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <!-- Testimonial Start -->
    <div style="background-color: #FCFCFC;" class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="mb-3 section-title">What our clients say</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item bg-light rounded">
                    <div class="bg-white border rounded p-4">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="site/img/testimonial-1.jpg"
                                style="width: 45px; height: 45px;">
                            <div class="ps-3">
                                <h6 class="fw-bold mb-1">Client Name</h6>
                            </div>
                        </div>
                        <input class="star-rate rating rating-loading" data-min="0" data-max="5" data-step="1"
                            value="2" data-size="xs" disabled="">
                        <p>
                            “Lorem ipsum dolor sit amet dolor sit consectetur eget maecenas sapien fusce egestas
                            risus purus suspendisse turpis volutpat onare”
                        </p>
                    </div>
                </div>

                <div class="testimonial-item bg-light rounded">
                    <div class="bg-white border rounded p-4">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="/site/img/testimonial-1.jpg"
                                style="width: 45px; height: 45px;">
                            <div class="ps-3">
                                <h6 class="fw-bold mb-1">Client Name</h6>
                            </div>
                        </div>
                        <input class="star-rate rating rating-loading" data-min="0" data-max="5" data-step="1"
                            value="2" data-size="xs" disabled="">
                        <p>
                            “Lorem ipsum dolor sit amet dolor sit consectetur eget maecenas sapien fusce egestas
                            risus purus suspendisse turpis volutpat onare”
                        </p>
                    </div>
                </div>

                <div class="testimonial-item bg-light rounded">
                    <div class="bg-white border rounded p-4">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="/site/img/testimonial-1.jpg"
                                style="width: 45px; height: 45px;">
                            <div class="ps-3">
                                <h6 class="fw-bold mb-1">Client Name</h6>
                            </div>
                        </div>
                        <input class="star-rate rating rating-loading" data-min="0" data-max="5" data-step="1"
                            value="2" data-size="xs" disabled="">
                        <p>
                            “Lorem ipsum dolor sit amet dolor sit consectetur eget maecenas sapien fusce egestas
                            risus purus suspendisse turpis volutpat onare”
                        </p>
                    </div>
                </div>

                <div class="testimonial-item bg-light rounded">
                    <div class="bg-white border rounded p-4">
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="/site/img/testimonial-1.jpg"
                                style="width: 45px; height: 45px;">
                            <div class="ps-3">
                                <h6 class="fw-bold mb-1">Client Name</h6>
                            </div>
                        </div>
                        <input class="star-rate rating rating-loading" data-min="0" data-max="5" data-step="1"
                            value="2" data-size="xs" disabled="">
                        <p>
                            “Lorem ipsum dolor sit amet dolor sit consectetur eget maecenas sapien fusce egestas
                            risus purus suspendisse turpis volutpat onare”
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <style>
        .text-danger {
            font-size: small !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $(document).ready(function() {
            $('#search-plans').on('click', function() {

                localStorage.removeItem('searchParams');
                localStorage.removeItem('searchResults');

                var check = 0;
                if (!$('#startDate').val()) {
                    $('#startDate-error').html("{{ __('dashboard.please select start date') }}");
                    check = 1;
                } else {
                    $('#startDate-error').html("");
                }

                // if (!$('#endDate').val()) {
                //     $('#endDate-error').html("{{ __('dashboard.please select end date') }}");
                //     check = 1;
                // } else {
                //     $('#endDate-error').html("");
                // }

                var start_date = new Date($('#startDate').val());
                var end_date = new Date($('#endDate').val());

                if ($('#endDate').val()) {
                    if (start_date >= end_date) {
                        $('#endDate-error').html(
                            "{{ __('dashboard.end date should be greater than start date') }}");
                        check = 1;

                    } else {
                        $('#endDate-error').html("");
                    }
                }


                if (!$('#adults').val() || $('#adults').val() == 0) {
                    $('#adults-error').html("{{ __('dashboard.please select number of adults') }}");
                    check = 1;

                } else {
                    $('#adults-error').html("");
                }

                if (!$('input[name="type"]:checked').val()) {
                    $('#trip-type-error').html("{{ __('dashboard.please select trip type') }}");
                    check = 1;
                } else {
                    $('#trip-type-error').html("");
                }

                if (!$('input[name="plans[]"]:checked').val()) {
                    $('#plans-error').html("{{ __('dashboard.please select plans') }}");
                    check = 1;
                } else {
                    $('#plans-error').html("");
                }

                if (check == 1) {
                    return false;
                }

                var plans = [];
                $.each($("input[name='plans[]']:checked"), function() {
                    plans.push($(this).val());
                });
                $('.search-page').hide();
                $('.prepare-page').show();
                lang = "{{ app()->getLocale() }}";
                event.preventDefault();
                $.ajax({
                    type: "post",
                    url: '/ai-trip',
                    data: {
                        'start_date': $('#startDate').val(),
                        'origin': $('#searchableSelect').val(),
                        'end_date': $('#endDate').val(),
                        'budget': $('#budget').val(),
                        'plans': plans,
                        'rooms': $('#rooms').val(),
                        'country_id': $('#country_id').val(),
                        'adults': $('#adults').val(),
                        'children': $('#children').val(),
                        'babies': $('#babies').val(),
                        'flight_class': $('#flight_class').val(),
                        'trip_type': $('input[name="type"]:checked').val(),
                        '_token': '{{ csrf_token() }}',
                    },
                    tryCount: 0,
                    retryLimit: 2,
                    dataType: "json",
                    success: function(response) {
                        $('#progress').css('width', '30%');
                        $('#second-step').addClass('active');
                        $('.search-page').hide();
                        $('.prepare-page').hide();
                        $('.plans-page').show();
                        localStorage.setItem('searchParams', JSON.stringify({
                            start_date: $('#startDate').val(),
                            origin: $('#searchableSelect').val(),
                            end_date: $('#endDate').val(),
                            budget: $('#budget').val(),
                            plans: plans,
                            rooms: $('#rooms').val(),
                            country_id: $('#country_id').val(),
                            adults: $('#adults').val(),
                            children: $('#children').val(),
                            babies: $('#babies').val(),
                            flight_class: $('#flight_class').val(),
                            trip_type: $('input[name="type"]:checked').val(),
                        }));
                        localStorage.setItem('searchResults', JSON.stringify(response.data));
                        trips = '';
                        response.data.forEach(function(trip) {
                            trips +=
                                '<div class="col-xxl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s" style="margin-bottom: 12px;">';
                            trips += '<div class="property-item overflow-hidden">';
                            trips +=
                                '<div class="bg-white text-primary d-flex justify-content-between align-items-center py-3 px-3">';
                            trips += '<p class="hotel-country m-0">';
                            trips += trip.country.name;
                            trips += ',';
                            trips += trip.city.name;
                            trips += '</p>';
                            trips += '</div>';
                            trips += '<div class="position-relative overflow-hidden">';
                            trips += '<a href="/plan/' + trip.id + '">';
                            trips += '<img class="img-fluid" src="' + trip.image +
                                '" alt="">';
                            trips += '</a>';
                            trips += '<div class="px-4 py-2 pb-0">';
                            trips +=
                                '<div class="hotel-title text-warning mb-3 d-flex justify-content-between align-items-center">';
                            trips +=
                                '<div class="align-items-center group-travel-interests">';
                            trip.travel_interests.forEach(function(interest) {
                                trips += '<p class="travel-interests">';
                                trips += interest.name;
                                trips += '</p>';
                            });
                            trips += '</div>';
                            trips += '</div>';
                            trips += '<p class="hotel-desc">';
                            trips += trip.description;
                            trips += '</p>';
                            trips += '</div>';
                            trips +=
                                '<div class="px-4 py-2 d-flex justify-content-between align-items-center border-top hotel-bottom">';
                            trips += '<small class="py-2 w-100">';
                            trips += '<a href="/plan/' + trip.id +
                                '" class="btn btn-warning py-2 w-100 px-3">{{ __('dashboard.view_details') }}</a>';
                            trips += '</small>';
                            trips += '</div>';
                            trips += '</div>';
                            trips += '</div>';
                            trips += '</div>';
                        });

                        $('#plans-container').html(trips);
                    },
                    error: function(response, textStatus, errorThrown) {
                        if (response.status == 504) {
                            this.tryCount++;
                            if (this.tryCount <= this.retryLimit) {
                                $.ajax(this);
                                return;
                            }
                            return;
                        }
                        $('.search-page').show();
                        $('.prepare-page').hide();
                    }
                });
            });

            $('#search-flights').on('click', function(e) {
                e.preventDefault();

                var check = 0;

                if (!$('#searchableSelectArrivalCity').val()) {
                    $('#flight-origin-error').html('{{ __('dashboard.please_select_origin') }}');
                    check = 1;
                } else {
                    $('#flight-origin-error').html('');
                }

                if (!$('#searchableSelectDepartureCity').val()) {
                    check = 1;
                    $('#flight-destination-error').html('{{ __('dashboard.please_select_destination') }}');
                } else {
                    $('#flight-destination-error').html('');
                }

                if (!$('#flight_adults').val()) {
                    check = 1;
                    $('#flight-travels-error').html('{{ __('dashboard.please_select_travelers') }}');
                } else {
                    $('#flight-travels-error').html('');
                }

                if (!$('#flight_start_date').val()) {
                    check = 0;
                    $('#flight-start-date-error').html('{{ __('dashboard.please_select_start_date') }}');
                } else {
                    $('#flight-start-date-error').html('');
                }

                // if (!$('#flight_end_date').val()) {
                //     check = 1;
                //     $('#flight-end-date-error').html('{{ __('dashboard.please_select_end_date') }}');
                // } else {
                //     $('#flight-end-date-error').html('');
                // }

                if (!$('#journey_type').val()) {
                    $('#flight-journey-type-error').html(
                        '{{ __('dashboard.please_select_journey_type') }}');
                } else {
                    $('#flight-journey-type-error').html('');
                }

                var start_date = new Date($('#flight_start_date').val());
                var end_date = new Date($('#flight_end_date').val());

                if ($('#flight_end_date').val()) {
                    if (start_date >= end_date) {
                        check = 1;
                        $('#flight-end-date-error').html(
                            "{{ __('dashboard.end date should be greater than start date') }}");
                    } else {
                        $('#flight-end-date-error').html('');
                    }
                }


                if (!$('#flight_budget').val()) {
                    check = 1;
                    $('#flight-budget-error').html('{{ __('dashboard.please_select_budget') }}');
                } else {
                    $('#flight-budget-error').html('');
                }

                if (check == 1) {
                    return;
                }

                $('#flight-form').submit();
            });

            $('#search-hotels').on('click', function(e) {
                e.preventDefault();

                var check = 0;

                if (!$('#searchableSelectArrivalLocation').val()) {
                    check = 1;
                    $('#hotel-destination-error').html('{{ __('dashboard.please_select_destination') }}');
                } else {
                    $('#hotel-destination-error').html('');
                }

                if (!$('#hotel_checkin').val()) {
                    check = 1;
                    $('#hotel-checkin-error').html('{{ __('dashboard.please_select_checkin_date') }}');
                } else {
                    $('#hotel-checkin-error').html('');
                }

                if (!$('#hotel_checkout').val()) {
                    check = 1;
                    $('#hotel-checkout-error').html('{{ __('dashboard.please_select_checkout_date') }}');
                } else {
                    $('#hotel-checkout-error').html('');
                }

                if (!$('#hotel_adults').val()) {
                    check = 1;
                    $('#hotel-travels-error').html('{{ __('dashboard.please_select_travelers') }}');
                } else {
                    $('#hotel-travels-error').html('');
                }


                if (!$('#hotel_rooms').val()) {
                    check = 1;
                    $('#hotel-travels-error').html('{{ __('dashboard.please_select_rooms') }}');
                } else {
                    $('#hotel-travels-error').html('');
                }

                if (!$('#hotel_budget').val()) {
                    check = 1;
                    $('#hotel-budget-error').html('{{ __('dashboard.please_select_budget') }}');
                } else {
                    $('#hotel-budget-error').html('');
                }

                var start_date = new Date($('#hotel_checkin').val());
                var end_date = new Date($('#hotel_checkout').val());

                if (start_date >= end_date) {
                    check = 1;
                    $('#hotel-checkout-error').html(
                        "{{ __('dashboard.end date should be greater than start date') }}");
                } else {
                    $('#hotel-checkout-error').html('');
                }

                if (check == 1) {
                    return;
                }


                $('#hotel-form').submit();
            });

            $(document).ready(function() {
                var searchResults = JSON.parse(localStorage.getItem('searchResults'));
                if (searchResults) {
                    displayResults(searchResults);
                } else {
                    window.location.href = "{{ route('ai-trip.index') }}";
                }

                function displayResults(data) {
                    $('#progress').css('width', '30%');
                    $('#second-step').addClass('active');
                    $('.search-page').hide();
                    $('.prepare-page').hide();
                    $('.plans-page').show();
                    var trips = '';
                    data.forEach(function(trip) {
                        trips += `
                    <div class="col-xxl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s" style="margin-bottom: 12px;">
                        <div class="property-item overflow-hidden">
                            <div class="bg-white text-primary d-flex justify-content-between align-items-center py-3 px-3">
                                <p class="hotel-country m-0">${trip.country.name}, ${trip.city.name}</p>
                            </div>
                            <div class="position-relative overflow-hidden">
                                <a href="/plan/${trip.id}">
                                    <img class="img-fluid" src="${trip.image}" alt="">
                                </a>
                                <div class="px-4 py-2 pb-0">
                                    <div class="hotel-title text-warning mb-3 d-flex justify-content-between align-items-center">
                                        <div class="align-items-center group-travel-interests">
                                            ${trip.travel_interests.map(interest => `<p class="travel-interests">${interest.name}</p>`).join('')}
                                        </div>
                                    </div>
                                    <p class="hotel-desc">${trip.description}</p>
                                </div>
                                <div class="px-4 py-2 d-flex justify-content-between align-items-center border-top hotel-bottom">
                                    <small class="py-2 w-100">
                                        <a href="/plan/${trip.id}" class="btn btn-warning py-2 w-100 px-3">{{ __('dashboard.view_details') }}</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    });

                    $('#plans-container').html(trips);
                }
            });

            $(document).ready(function() {
                $('#searchableSelect').select2({
                    placeholder: 'Search...',
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    minimumInputLength: 3,
                    ajax: {
                        url: '/search-airport',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.city + ' (' + item.airport_code +
                                            ') ',
                                    }; // Adjust based on API response structure
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#searchableSelectArrivalLocation').select2({
                    placeholder: 'Search...',
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    ajax: {
                        url: '/search-airport',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.city + ' (' + item.airport_code +
                                            ') ',
                                    }; // Adjust based on API response structure
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#searchableSelectArrivalCity').select2({
                    placeholder: 'Search...',
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    ajax: {
                        url: '/search-airport',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.city + ' (' + item.airport_code +
                                            ') ',
                                    }; // Adjust based on API response structure
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#searchableSelectDepartureCity').select2({
                    placeholder: 'Search...',
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function () {
                            return ''; // Return empty string to hide the message
                        }
                    },
                    ajax: {
                        url: '/search-airport',
                        dataType: 'json',
                        delay: 250, // Delay in ms for making API call
                        data: function(params) {
                            return {
                                city_name: params.term // The search term entered by the user
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.city + ' (' + item.airport_code +
                                            ') ',
                                    }; // Adjust based on API response structure
                                })
                            };
                        },
                        cache: true
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            function toggleOneWayDiv() {
                const selectedValue = $('#journey_type').val();
                if (selectedValue === '1') {
                    $('.one_way').hide();
                } else {
                    $('.one_way').show();
                }
            }
            toggleOneWayDiv();
            $('#journey_type').on('change', function() {
                toggleOneWayDiv();
            });
        });
    </script>
@endsection
