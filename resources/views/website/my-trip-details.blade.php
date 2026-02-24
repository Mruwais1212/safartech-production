@extends('website.layout')
@section('title', __('dashboard.My Trips Details'))
@section('newCss')
    <style>
        .print-title {
            display: none
        }

        /* Room Details Styles */
        .modern-room-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .room-types {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .room-type-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 500;
        }

        .modern-table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .table-label {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            border-right: 3px solid #007bff;
        }

        .table-value {
            background: white;
            color: #6c757d;
        }

        .modern-amenities-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 0.75rem;
        }

        .amenity-item:hover {
            background: #f8f9fa;
            border-color: #ffc107;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
        }

        .amenity-item i {
            flex-shrink: 0;
            width: 20px;
            text-align: center;
        }

        .modern-cancel-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }

        .policy-item {
            margin-bottom: 15px;
        }

        .policy-card {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .policy-date, .policy-charge, .policy-type {
            margin-bottom: 8px;
        }

        .policy-date:last-child, .policy-charge:last-child, .policy-type:last-child {
            margin-bottom: 0;
        }

        /* Room info card improvements */
        .room-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Status indicators */
        .status-indicator {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-refundable {
            background-color: #d4edda;
            color: #155724;
        }

        .status-non-refundable {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* New Room Type Card Styling */
        .room-type-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .room-type-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.4s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }

        .room-type-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ffc107, #ffb300);
        }

        .room-type-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
            border-color: #ffc107;
        }

        .room-type-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffc107, #ffb300);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        .room-type-icon i {
            font-size: 1.5rem;
            color: #fff;
        }

        .room-type-content {
            flex: 1;
        }

        .room-type-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .room-type-badge-new {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: #fff;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
        }

        /* Cancellation Policy Card Styling */
        .cancellation-policy-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .cancellation-policy-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .cancellation-policy-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: #dc3545;
        }

        .policy-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: #fff;
        }

        .policy-number {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
        }

        .policy-date {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .date-label {
            font-weight: 600;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .date-value {
            font-weight: 700;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .policy-content {
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .policy-item {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .policy-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #495057;
            font-size: 0.95rem;
        }

        .policy-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .charge-type {
            color: #dc3545;
            background: #f8d7da;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            text-transform: capitalize;
        }

        .percentage-badge {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 700;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            min-width: 80px;
            text-align: center;
        }

        .amount-badge {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 700;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            min-width: 100px;
            text-align: center;
        }

        /* RTL Support */
        [dir="rtl"] .room-type-card {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .room-type-content {
            text-align: right;
        }

        [dir="rtl"] .policy-header {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .policy-date {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .policy-item {
            text-align: right;
        }

        [dir="rtl"] .policy-label {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .amenity-item {
            direction: rtl;
            text-align: right;
        }

        [dir="rtl"] .amenity-item i {
            margin-right: 0;
            margin-left: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .room-type-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .room-type-card {
                padding: 1.5rem;
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .room-type-icon {
                width: 50px;
                height: 50px;
            }

            .room-type-icon i {
                font-size: 1.25rem;
            }

            .room-type-title {
                font-size: 1.1rem;
            }

            .policy-header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .policy-date {
                justify-content: center;
            }

            .policy-content {
                padding: 1.5rem;
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .amenities-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .amenity-item {
                font-size: 0.85rem;
                padding: 0.6rem 0.8rem;
                gap: 0.5rem;
            }

            .amenity-item i {
                font-size: 0.9rem;
            }

            [dir="rtl"] .room-type-card {
                flex-direction: column;
            }

            [dir="rtl"] .policy-header {
                flex-direction: column;
            }
        }

        @media print {

            .header,
            .hotel-details-form,
            .nav-bar,
            .back-to-top,
            .footer,
            .print-section {
                display: none;
            }

            .print-title {
                display: inline-block;
            }

            .mt-5 {
                margin-top: 0rem !important;
            }
        }
    </style>
@endsection
@section('content')
    <div style="background-image: url('/site/img/summary.png');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-12 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.My Trips') }} /
                                {{ __('dashboard.reservation number') }} {{ $reservation->id }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container px-5 last-step mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="plan-section">

                            <div class="header-summery justify-content-start">
                                <h3 class="text-dark">
                                    @if (isset($reservation->type) && in_array($reservation->type, [1]))
                                        {{ __('dashboard.flight and hotel trip') }}
                                        {{ __('dashboard.from') }}
                                        {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                        ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                        {{ __('dashboard.to') }}
                                        {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                        ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                    @endif

                                    @if (isset($reservation->type) && in_array($reservation->type, [2]))
                                        {{ __('dashboard.hotel booking only') }}
                                        {{ __('dashboard.to') }}
                                        {{ @$reservation->hotel->hotel_name }}
                                    @endif

                                    @if (isset($reservation->type) && in_array($reservation->type, [3]))
                                        {{ __('dashboard.flight only trip') }}
                                        {{ __('dashboard.from') }}
                                        {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                        ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                        {{ __('dashboard.to') }}
                                        {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                        ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                    @endif

                                    <small class="print-title"> - {{ __('dashboard.reservation number') }}
                                        {{ $reservation->id }}</small>
                                </h3>
                            </div>

                            {{-- <div class="plan-gallery">
                                <div class="container p-0">
                                    <div class="row justify-content-start">
                                        <div class="col col-md-12 gallery-container-wrap position-relative">
                                            <button type="button" class="btn btn-dynamic d-none"
                                                id="dynamic-mode-images"></button>
                                            <div class="gallery-container" id="gallery-dynamic-thumbnails">
                                                <a class="gallery-item w-100" data-index="0"
                                                    data-src="{{ asset('site/img/gallery1.png') }}">
                                                    <img alt="layers of blue." class="img-responsive"
                                                        src="/site/img/gallery1.png" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <hr>
                            <div class="plan-details">
                                <div class="plan-details-title">
                                    <h6>{{ __('dashboard.Traveling details') }}</h6>
                                    {{-- <small class="plan-advice text-warning">
                                        -12°C - Cold
                                    </small> --}}
                                </div>
                                <p class="details">
                                    @if (isset($reservation->type) && in_array($reservation->type, [1]))
                                        {{ __('dashboard.this is a flight and hotel trip') }}
                                        {{ __('dashboard.from') }}
                                        {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                        ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                        {{ __('dashboard.to') }}
                                        {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                        ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                    @endif

                                    @if (isset($reservation->type) && in_array($reservation->type, [2]))
                                        {{ __('dashboard.this is hotel booking only') }}
                                        {{ __('dashboard.to') }}
                                        {{ @$reservation->hotel->hotel_name }}
                                    @endif

                                    @if (isset($reservation->type) && in_array($reservation->type, [3]))
                                        {{ __('dashboard.this is a flight booking only') }}
                                        {{ __('dashboard.from') }}
                                        {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                                        ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                                        {{ __('dashboard.to') }}
                                        {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                                        ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})
                                    @endif
                                </p>
                            </div>
                            <hr>
                            {{-- <div class="plan-details">
                                <div class="plan-details-title">
                                    <h6>Advices for this trip</h6>
                                </div>
                                <ul class="details ps-3">
                                    <li>
                                        Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                        tellus dui
                                        purus
                                    </li>
                                    <li>
                                        Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                        tellus dui
                                        purus
                                    </li>
                                    <li>
                                        Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                        tellus dui
                                        purus
                                    </li>
                                    <li>
                                        Lorem ipsum dolor sit amet consectetur. Faucibus habitasse ac
                                        tellus dui
                                        purus
                                    </li>
                                </ul>
                            </div> --}}
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="plan-section">

                            <div class="header-summery">
                                <h3 class="text-dark">{{ __('dashboard.Passenger Details') }}</h3>
                            </div>

                            <hr>
                            <div class="plan-details">
                                <div class="plan-details-title">
                                    <h6>{{ __('dashboard.Traveling details') }}</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center passenger-table ">
                                        <thead>
                                            <tr>
                                                <th class="text-warning" scope="col">
                                                    {{ __('dashboard.Name') }}</th>
                                                <th class="text-warning" scope="col">
                                                    {{ __('dashboard.Passport') }}</th>
                                                <th class="text-warning" scope="col">
                                                    {{ __('dashboard.Phone Number') }}</th>
                                                <th class="text-warning" scope="col">
                                                    {{ __('dashboard.Gender') }}</th>
                                                <th class="text-warning" scope="col">
                                                    {{ __('dashboard.Age') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reservation->passengers as $passenger)
                                                <tr>
                                                    <td>{{ @$passenger->first_name }}
                                                        {{ @$passenger->last_name }}
                                                    </td>
                                                    <td>{{ @$passenger->passport_number }}</td>
                                                    <td>{{ @$passenger->phone }}</td>
                                                    <td>
                                                        {{ @$passenger->gender == 1 ? 'Male' : 'Female' }}
                                                    </td>
                                                    @php
                                                        $dob = new DateTime(@$passenger->birth_date);
                                                        $today = new DateTime('today');
                                                        $year = $dob->diff($today)->y;
                                                        $month = $dob->diff($today)->m;
                                                        $day = $dob->diff($today)->d;
                                                    @endphp

                                                    <td>{{ $year . ' سنة  و  ' . $month . '  شهر   ' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($reservation->type == 1 || $reservation->type == 2)
                        <div @if($reservation->hotel->status == 2 || $reservation->hotel->status == 3) style="opacity:0.7" @endif class="col-md-12 mt-5">
                            <div class="plan-section">
                                <div class="header-summery">
                                    <h3 class="text-dark">{{ __('dashboard.Hotel Details') }}</h3>
                                    @if($reservation->hotel->status == 1)
                                    <button type="button" class="text-white btn btn-danger fw-bold" data-bs-toggle="modal"
                                        data-bs-target="#CancelModal">
                                        {{ __('trans.cancel_reservation') }}
                                    </button>
                                    @endif
                                    @if($reservation->hotel->status == 2)
                                    <p class="alert alert-danger m-0" >
                                        {{ __('trans.cancel_pending') }}
                                    </p>
                                    @endif
                                    @if($reservation->hotel->status == 3 && $reservation->hotel->is_refundable == 1)
                                    <p class="alert alert-success m-0" >
                                        {{ __('trans.reservation_refunded') }}
                                    </p>
                                    @endif
                                    @if($reservation->hotel->status == 3 && $reservation->hotel->is_refundable == 0)
                                    <p class="alert alert-success m-0" >
                                        {{ __('trans.reservation_canceled') }}
                                    </p>
                                    @endif

                                    <div class="modal fade" id="CancelModal" tabindex="-1"
                                        aria-labelledby="CancelModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="CancelModalLabel">
                                                        {{ $reservation->hotel->hotel_name }}
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    @if($reservation->hotel->is_refundable == 0)
                                                    {{ __('trans.will_cancel_without_tax') }}
                                                    @else
                                                    {{ __('trans.will_cancel_with_tax') }}
                                                    @endif
                                                </div>
                                                <div class="modal-footer d-flex justify-content-between">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">{{ __('trans.close') }}</button>
                                                    <form method="POST" action="{{ url('cancel-hotel',$reservation->hotel->booking_code) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary">{{ __('trans.save_cancel') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="plan-details hotel-detailss">
                                    <div class="hotel-details-form">
                                        <div class="gallery-container position-relative">
                                            @if($reservation->hotel->image)
                                            <a class="gallery-item w-100" data-index="0"
                                                data-src="{{ $reservation->hotel->image }}">
                                                <img alt="layers of blue." class="img-responsive"
                                                    src="{{ $reservation->hotel->hotel_image }}" />
                                            </a>
                                            @else
                                            <a class="gallery-item w-100" data-index="0"
                                                data-src="/no-image.png">
                                                <img alt="layers of blue." class="img-responsive"
                                                    src="/no-image.png" />
                                            </a>
                                            @endif
                                            <div style="opacity: 0" class="top-title">
                                                <h3>{{ $reservation->hotel->hotel_name }}</h3>
                                                <p>{{ $reservation->hotel->hotel_address }}</p>
                                            </div>
                                            <div style="opacity: 0" class="bottom-title">
                                                <h3>{{ __('dashboard.Hotel Roles') }}</h3>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p>
                                                        {{ __('dashboard.Check-In') }} :
                                                        {{ $reservation->hotel->check_in }}
                                                        {{ __('dashboard.Check-Out') }} :
                                                        {{ $reservation->hotel->check_out }}
                                                    </p>
                                                    {{-- <a class="text-warning fw-bold" href="hotel-single.html">
                                                    Visit website
                                                    <i class="bi bi-arrow-right"></i>
                                                </a> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center passenger-table ">
                                            <thead>
                                                <tr>
                                                    <th class="text-warning" scope="col">
                                                        {{ $reservation->hotel->hotel_name }}
                                                    </th>
                                                    <th class="text-warning" scope="col">
                                                        {{ __('dashboard.Hotel Roles') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $reservation->hotel->hotel_address }}
                                                        @if($reservation->hotel->phone)
                                                            <br><strong>{{ __('dashboard.hotel_phone') }}:</strong> {{ $reservation->hotel->phone }}
                                                        @endif
                                                    </td>
                                                    <td>{{ __('dashboard.Check-In') }} :
                                                        {{ $reservation->hotel->check_in }}
                                                        {{ __('dashboard.Check-Out') }} :
                                                        {{ $reservation->hotel->check_out }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Room Details Section --}}
                        @if($reservation->hotel && $reservation->hotel->room_details)
                            @php
                                $room_details = is_string($reservation->hotel->room_details) ? 
                                    json_decode($reservation->hotel->room_details, true) : 
                                    $reservation->hotel->room_details;
                            @endphp
                            @if($room_details)
                                <div class="col-md-12 mt-4">
                                    <div class="plan-section">
                                        <div class="header-summery">
                                            <h3 class="text-dark">{{ __('dashboard.Room Details') }}</h3>
                                        </div>
                                        <div class="plan-details">
                                            {{-- Room Type Cards --}}
                                            @if(isset($room_details['Name']) && is_array($room_details['Name']))
                                                <div class="room-type-container">
                                                    @foreach($room_details['Name'] as $name)
                                                        <div class="room-type-card">
                                                            <div class="room-type-icon">
                                                                <i class="fas fa-bed"></i>
                                                            </div>
                                                            <div class="room-type-content">
                                                                <div class="room-type-title">{{ $name }}</div>
                                                                <div class="room-type-badge-new">{{ __('dashboard.Selected Room') }}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Room Information Table --}}
                                            <div class="table-responsive mb-4">
                                                <table class="table table-bordered passenger-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-warning" scope="col">{{ __('dashboard.Room Information') }}</th>
                                                            <th class="text-warning" scope="col">{{ __('dashboard.Details') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($room_details['Inclusion']) && $room_details['Inclusion'])
                                                            <tr>
                                                                <td><strong>{{ __('dashboard.Inclusion') }}</strong></td>
                                                                <td>{{ $room_details['Inclusion'] }}</td>
                                                            </tr>
                                                        @endif
                                                        @if(isset($room_details['MealType']) && $room_details['MealType'])
                                                            <tr>
                                                                <td><strong>{{ __('dashboard.Meal Type') }}</strong></td>
                                                                <td>{{ str_replace('_', ' ', $room_details['MealType']) }}</td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <td><strong>{{ __('dashboard.Refundable') }}</strong></td>
                                                            <td>
                                                                @if(isset($room_details['IsRefundable']) && $room_details['IsRefundable'])
                                                                    <span class="status-indicator status-refundable">{{ __('dashboard.Yes') }}</span>
                                                                @else
                                                                    <span class="status-indicator status-non-refundable">{{ __('dashboard.No') }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- Amenities Section --}}
                                            @if(isset($room_details['Amenities']) && is_array($room_details['Amenities']) && count($room_details['Amenities']) > 0)
                                                <div class="plan-details-title">
                                                    <h6>{{ __('dashboard.Room Amenities') }}</h6>
                                                </div>
                                                <div class="room-info-card">
                                                    <div class="amenities-grid">
                                                        @foreach($room_details['Amenities'] as $amenity)
                                                            <div class="amenity-item">
                                                                <i class="fas fa-check-circle text-success"></i>
                                                                <span>{{ $amenity }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Cancellation Policies --}}
                                            @if(isset($room_details['CancelPolicies']) && is_array($room_details['CancelPolicies']) && count($room_details['CancelPolicies']) > 0)
                                                <div class="plan-details-title">
                                                    <h6>{{ __('dashboard.Cancellation Policies') }}</h6>
                                                </div>
                                                <div class="cancellation-policy-container">
                                                    @foreach($room_details['CancelPolicies'] as $index => $policy)
                                                        <div class="cancellation-policy-card">
                                                            <div class="policy-header">
                                                                <div class="policy-number">{{ $index + 1 }}</div>
                                                                <div class="policy-date">
                                                                    @if(isset($policy['FromDate']))
                                                                        <div>
                                                                            <span class="date-label">{{ __('dashboard.From') }}:</span>
                                                                            <span class="date-value">{{ $policy['FromDate'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                    @if(isset($policy['ToDate']))
                                                                        <div>
                                                                            <span class="date-label">{{ __('dashboard.To') }}:</span>
                                                                            <span class="date-value">{{ $policy['ToDate'] }}</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="policy-content">
                                                                @if((isset($policy['Charge']) && $policy['Charge'] !== null && $policy['Charge'] !== '') || (isset($policy['CancellationCharge']) && $policy['CancellationCharge'] !== null && $policy['CancellationCharge'] !== ''))
                                                                    <div class="policy-item">
                                                                        <div class="policy-label">
                                                                            <i class="fas fa-percent"></i>
                                                                            {{ __('dashboard.Cancellation Charge') }}
                                                                        </div>
                                                                        <div class="policy-value">
                                                                            @php
                                                                                $chargeValue = $policy['CancellationCharge'] ?? $policy['Charge'] ?? 0;
                                                                            @endphp
                                                                            @if(isset($policy['ChargeType']) && $policy['ChargeType'] == 'Percentage')
                                                                                <span class="percentage-badge">{{ number_format($chargeValue, 0) }}%</span>
                                                                            @elseif(isset($policy['ChargeType']) && $policy['ChargeType'] == 'Amount' || $policy['ChargeType'] == 'Fixed')
                                                                                <span class="amount-badge">{{ number_format($chargeValue, 2) }} {{ __('dashboard.Currency') }}</span>
                                                                            @else
                                                                                <span class="amount-badge">{{ $chargeValue }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if(isset($policy['ChargeAmount']) && $policy['ChargeAmount'] !== null && $policy['ChargeAmount'] !== '')
                                                                    <div class="policy-item">
                                                                        <div class="policy-label">
                                                                            <i class="fas fa-money-bill-wave"></i>
                                                                            {{ __('dashboard.Charge Amount') }}
                                                                        </div>
                                                                        <div class="policy-value">
                                                                            <span class="amount-badge">{{ number_format($policy['ChargeAmount'], 2) }} {{ __('dashboard.Currency') }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if(isset($policy['ChargeType']) && $policy['ChargeType'] !== null && $policy['ChargeType'] !== '')
                                                                    <div class="policy-item">
                                                                        <div class="policy-label">
                                                                            <i class="fas fa-info-circle"></i>
                                                                            {{ __('dashboard.Charge Type') }}
                                                                        </div>
                                                                        <div class="policy-value">
                                                                            <span class="charge-type">{{ ucfirst(str_replace('_', ' ', $policy['ChargeType'])) }}</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endif

                    @if ($reservation->type == 3 || $reservation->type == 1)
                        <div class="col-md-12 mt-5">
                            <div class="plan-section">
                                <div class="header-summery">
                                    <h3 class="text-dark">{{ __('dashboard.Flight Details') }}</h3>
                                    <a class="btn btn-warning" href="/flight-tickets/{{$reservation->id}}" style="float: right"> طباعة الحجز</a>
                                @if($reservation->flight[0]->status == 1)
                                    <button type="button" class="text-white btn btn-danger fw-bold" data-bs-toggle="modal"
                                        data-bs-target="#CancelModall">
                                        {{ __('trans.cancel_reservation') }}
                                    </button>
                                    @endif

                                @if($reservation->flight[0]->status == 2)
                                    <p class="alert alert-danger m-0" >
                                        {{ __('trans.cancel_pending') }}
                                    </p>
                                    @endif
                                    @if($reservation->flight[0]->status == 3 && $reservation->flight[0]->is_refundable == 1)
                                    <p class="alert alert-success m-0" >
                                        {{ __('trans.reservation_refunded') }}
                                    </p>
                                    @endif
                                    @if($reservation->flight[0]->status == 3 && $reservation->flight[0]->is_refundable == 0)
                                    <p class="alert alert-success m-0" >
                                        {{ __('trans.reservation_canceled') }}
                                    </p>
                                    @endif
                                </div>

                                <div class="modal fade" id="CancelModall" tabindex="-1"
                                        aria-labelledby="CancelModallLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    @if($reservation->flight[0]->is_refundable == 0)
                                                    {{ __('trans.will_cancel_without_tax') }}
                                                    @else
                                                    {{ __('trans.will_cancel_with_tax') }}
                                                    @endif
                                                    {{$reservation->flight[0]->pnr}}
                                                </div>
                                                <div class="modal-footer d-flex justify-content-between">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">{{ __('trans.close') }}</button>
                                                    <form method="POST" action="{{ url('cancel-flight',$reservation->flight[0]->pnr) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary">{{ __('trans.save_cancel') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <div class="plan-details flight-details">
                                    <div class="row">
                                        <div class="col-md-12">
                                        @foreach($reservation->flights as $main_flight)
                                                <h3 class="text-dark mb-4">
                                                    {{ __('dashboard.Flight Path') }} : <span
                                                        class="text-warning">
                                                        {{$main_flight->flight_from .' - '.$main_flight->flight_to}}
{{--                                                        {{ $key == 0 ? __('dashboard.inboard') : __('dashboard.outboard') }}--}}
                                                    </span>
                                                </h3>
                                            @foreach ($main_flight->segments as $key => $flight)
                                                <div class="airway-details">
                                                    <div class="flight-time">
                                                        <div class="time">
                                                            {{ $flight->origin->city_name }}
                                                            ({{ $flight->origin->country_name }})
                                                        </div>
                                                        <div class="time-code">
                                                            {{ \Carbon\Carbon::parse($flight->origin->arr_time)->format('d-m-Y H:i A') }}
                                                        </div>
                                                    </div>
                                                    <div class="flight-road">
                                                        <div class="road">
                                                            <span>{{ $flight->duration }}</span>
                                                            {{ __('dashboard.Min') }}</span>
                                                            <hr class="my-2">
                                                            <span>{{ $flight->airline->airline_name }}</span>
                                                        </div>
                                                        <i class="fas fa-plane"></i>
                                                    </div>
                                                    <div class="flight-time">
                                                        <div class="time">
                                                            {{ $flight->destination->city_name }}
                                                            ({{ $flight->destination->country_name }})
                                                        </div>
                                                        <div class="time-code">
                                                            {{ \Carbon\Carbon::parse($flight->destination->arr_time)->format('d-m-Y H:i A') }}
                                                        </div>
                                                    </div>
                                                    <div class="plan-details">
                                                        <div class="plan-details-title">
                                                            <h6 style="font-size: 12px;">
                                                                {{ __('dashboard.Flight Class') }} :
                                                                <span class="text-warning">
                                                                    @if ($flight->cabin_class == 2)
                                                                        {{ __('dashboard.Economy') }}
                                                                    @endif

                                                                    @if ($flight->cabin_class == 3)
                                                                        {{ __('dashboard.PremiumEconomy') }}
                                                                    @endif

                                                                    @if ($flight->cabin_class == 4)
                                                                        {{ __('dashboard.Business') }}
                                                                    @endif

                                                                    @if ($flight->cabin_class == 5)
                                                                        {{ __('dashboard.PremiumBusiness') }}
                                                                    @endif

                                                                    @if ($flight->cabin_class == 6)
                                                                        {{ __('dashboard.First Class') }}
                                                                    @endif

                                                                </span>
                                                            </h6>
                                                        </div>
                                                        <div class="plan-details-title" style="font-size: 12px;">
                                                            <h6 style="font-size: 12px;">
                                                                {{ __('dashboard.Bags') }}
                                                                <br>
                                                                <span class="text-warning"
                                                                    style="font-size: 12px;">
                                                                    <span class="text-success">{{ __('dashboard.bag_weight') }}:</span>
                                                                    {{ $flight->baggage  }}</span>
                                                                <br>
                                                                <span class="text-warning"
                                                                    style="font-size: 12px;">
                                                                    <span class="text-success">{{ __('dashboard.bag_hand') }}:</span>
                                                                    {{ $flight->cabin_baggage }}</span>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            @endforeach
                                            
                                            {{-- Flight Cancellation Policy Section --}}
                                            <div class="mt-4">
                                                <h4 class="text-dark">{{ __('dashboard.Cancellation Policy') }}</h4>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered passenger-table">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-warning" scope="col">{{ __('dashboard.Policy Details') }}</th>
                                                                <th class="text-warning" scope="col">{{ __('dashboard.Information') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><strong>{{ __('dashboard.Refundable') }}</strong></td>
                                                                <td>
                                                                    @if($main_flight->is_refundable)
                                                                        <span class="badge bg-success">{{ __('dashboard.Yes') }}</span>
                                                                        <small class="d-block mt-1 text-muted">{{ __('dashboard.refund_with_charges') }}</small>
                                                                    @else
                                                                        <span class="badge bg-danger">{{ __('dashboard.No') }}</span>
                                                                        <small class="d-block mt-1 text-muted">{{ __('dashboard.non_refundable_ticket') }}</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>{{ __('dashboard.Ticket Type') }}</strong></td>
                                                                <td>
                                                                    @if($main_flight->is_lcc)
                                                                        {{ __('dashboard.Low Cost Carrier') }} (LCC)
                                                                        <small class="d-block mt-1 text-muted">{{ __('dashboard.lcc_restrictions_apply') }}</small>
                                                                    @else
                                                                        {{ __('dashboard.Full Service Carrier') }}
                                                                        <small class="d-block mt-1 text-muted">{{ __('dashboard.standard_airline_policies') }}</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>{{ __('dashboard.Cancellation Terms') }}</strong></td>
                                                                <td>
                                                                    @if($main_flight->is_refundable)
                                                                        {{ __('dashboard.cancellation_allowed_with_charges') }}<br>
                                                                        <small class="text-muted">{{ __('dashboard.processing_time_7_14_days') }}</small>
                                                                    @else
                                                                        {{ __('dashboard.no_cancellation_allowed') }}<br>
                                                                        <small class="text-muted">{{ __('dashboard.changes_subject_to_airline_policy') }}</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @if($main_flight->last_ticket_date)
                                                            <tr>
                                                                <td><strong>{{ __('dashboard.Last Ticketing Date') }}</strong></td>
                                                                <td>{{ \Carbon\Carbon::parse($main_flight->last_ticket_date)->format('d M Y, H:i') }}</td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-12 mt-5">
                        <div class="plan-section">

                            <div class="header-summery">
                                <h3 class="text-dark">{{ __('dashboard.Payment details') }}</h3>
                            </div>

                            <div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <ul class="summary-prices-list">
                                            @if (@$reservation->flight->count() > 0)
                                                <li>
                                                    <span>{{ __('dashboard.Flight') }} : </span>
                                                    <span>{{ @$reservation->flight[0]->total_price }}
                                                        {{ __('dashboard.' . $reservation->currency) }}</span>
                                                </li>
                                            @endif
                                            @if (@$reservation->hotel)
                                                <li>
                                                    <span>{{ __('dashboard.Hotel') }} : </span>
                                                    <span>{{ @$reservation->hotel->price }}
                                                        {{ __('dashboard.' . $reservation->hotel->currency) }}</span>
                                                </li>
                                            @endif
                                            <li>
                                                <span>{{ __('trans.taxes') }} : </span>
                                                <span>{{ @$reservation->hotel->tax_amount +  @$reservation->flight[0]->tax_amount}}
                                                    {{ __('dashboard.' . $reservation->currency) }}</span>
                                            </li>
                                            <li class="total-price">
                                                <span>{{ __('dashboard.Total') }} : </span>
                                                <span>{{ @$reservation->hotel->price_with_tax +  @$reservation->flight[0]->price_with_tax }}
                                                    {{ __('dashboard.' . $reservation->currency) }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="print-section my-4">
                <a href="/export-trip/{{$reservation->id}}"
                    class="btn btn-warning py-3 w-100 fw-bold">{{ __('dashboard.Export Trip Details PDF') }}</a>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('newJs')
<script>

</script>
@endsection
