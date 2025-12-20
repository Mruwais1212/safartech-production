<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trip Details - {{$reservation->uuid}}</title>
    <style>
        @page {
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #000;
            line-height: 1.6;
            margin: 0;
        }

        .container {
            padding: 15px;
            border: 1px solid #ccc;
        }

        h1, h2, h3 {
            color: #003399;
            margin-bottom: 10px;
        }

        .logo {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .logo img {
            height: 70px;
        }

        .logo > div {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }

        .trip-title {
            text-align: right;
        }

        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table thead {
            background-color: #f0f8ff;
        }

        th, td {
            border: 1px solid #999;
            padding: 10px;
            font-size: 12px;
            vertical-align: top;
            word-break: break-word;
        }

        th {
            background-color: #e6f0ff;
            font-weight: bold;
            text-align: left;
            font-size: 11px;
        }

        .info-table th {
            width: 30%;
            background-color: #f8f9fa;
            border-right: 3px solid #007bff;
        }

        .info-table td {
            width: 70%;
            background-color: white;
        }

        .bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .price-box {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin: 15px 0;
        }

        .price-amount {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .page-break {
            page-break-before: always;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        .note {
            font-style: italic;
            font-size: 11px;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header with Logo -->
    <div class="logo">
        <img src="{{public_path('/site/img/logo.png')}}" alt="Safartech Logo">
        <div class="trip-title">
            <h1>Trip Details</h1>
            <p><strong>Booking Reference:</strong> {{$reservation->uuid}}</p>
            <p><strong>Reservation ID:</strong> {{$reservation->id}}</p>
        </div>
    </div>

    <!-- Trip Overview -->
    <div class="section">
        <h2>Trip Overview</h2>
        <table class="info-table">
            <tr>
                <th>Trip Type</th>
                <td>{{$reservation->getType()}}</td>
            </tr>
            <tr>
                <th>Booking Date</th>
                <td>{{$reservation->created_at->format('d M Y, H:i')}}</td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td class="bold">{{number_format($reservation->price, 2)}} {{$reservation->currency}}</td>
            </tr>
            <tr>
                <th>Payment Status</th>
                <td>
                    @if($reservation->payment_method == 1)
                        <span class="status-badge status-confirmed">Paid</span>
                    @else
                        <span class="status-badge status-pending">Pending</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Customer</th>
                <td>{{$reservation->user->name}} ({{$reservation->user->email}})</td>
            </tr>
        </table>
    </div>

    <!-- Trip Description -->
    <div class="section">
        <h2>Trip Description</h2>
        <p>
            @if (isset($reservation->type) && in_array($reservation->type, [1]))
                This is a complete travel package including flight and hotel accommodation from 
                {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                to 
                {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                ({{ @$reservation->flight[0]->segments[0]->destination->country_name }}).
            @endif

            @if (isset($reservation->type) && in_array($reservation->type, [2]))
                This is a hotel-only booking for {{ @$reservation->hotel->hotel_name }}.
            @endif

            @if (isset($reservation->type) && in_array($reservation->type, [3]))
                This is a flight-only booking from 
                {{ @$reservation->flight[0]->segments[0]->origin->city_name }}
                ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})
                to 
                {{ @$reservation->flight[0]->segments[0]->destination->city_name }}
                ({{ @$reservation->flight[0]->segments[0]->destination->country_name }}).
            @endif
        </p>
    </div>

    <!-- Passengers Information -->
    @if($reservation->passengers && count($reservation->passengers) > 0)
    <div class="section">
        <h2>Passenger Information</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Passport Number</th>
                    <th>Nationality</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservation->passengers as $passenger)
                <tr>
                    <td>
                        @php
                            $title_text = '';
                            if($passenger->title == 1) {
                                $title_text = 'Mr';
                            } elseif($passenger->title == 2) {
                                $title_text = 'Mrs';
                            } else {
                                $title_text = $passenger->title;
                            }
                        @endphp
                        {{$title_text}}. {{ $passenger->first_name }} {{ $passenger->last_name }}
                    </td>
                    <td>{{ $passenger->gender == 1 ? 'Male' : 'Female' }}</td>
                    <td>
                        @php
                            $dob = new DateTime($passenger->birth_date);
                            $today = new DateTime('today');
                            $age = $dob->diff($today)->y;
                        @endphp
                        {{ $age }} years
                    </td>
                    <td>{{ $passenger->phone }}</td>
                    <td>{{ $passenger->passport_number ?: 'N/A' }}</td>
                    <td>{{ $passenger->nationality ?: 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Hotel Details -->
    @if ($reservation->type == 1 || $reservation->type == 2)
    <div class="section">
        <h2>Hotel Details</h2>
        <table class="info-table">
            <tr>
                <th>Hotel Name</th>
                <td class="bold">{{ @$reservation->hotel->hotel_name }}</td>
            </tr>
            <tr>
                <th>Hotel Phone</th>
                <td>{{ @$reservation->hotel->phone ?: 'N/A' }}</td>
            </tr>
            <tr>
                <th>Check-in Date</th>
                <td>{{ @$reservation->hotel->check_in }}</td>
            </tr>
            <tr>
                <th>Check-out Date</th>
                <td>{{ @$reservation->hotel->check_out }}</td>
            </tr>
            <tr>
                <th>Room Type</th>
                <td>{{ @$reservation->hotel->room_type }}</td>
            </tr>
            <tr>
                <th>Number of Rooms</th>
                <td>{{ @$reservation->hotel->rooms }}</td>
            </tr>
            <tr>
                <th>Hotel Status</th>
                <td>
                    @if($reservation->hotel->status == 1)
                        <span class="status-badge status-confirmed">Confirmed</span>
                    @elseif($reservation->hotel->status == 2)
                        <span class="status-badge status-pending">Cancellation Pending</span>
                    @elseif($reservation->hotel->status == 3)
                        <span class="status-badge status-cancelled">
                            {{ $reservation->hotel->is_refundable == 1 ? 'Refunded' : 'Cancelled' }}
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Hotel Price</th>
                <td class="bold">{{ number_format(@$reservation->hotel->price, 2) }} {{ @$reservation->hotel->currency }}</td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Flight Details -->
    @if ($reservation->type == 3 || $reservation->type == 1)
    <div class="section page-break">
        <h2>Flight Details</h2>
        @foreach($reservation->flights as $main_flight)
            <div class="mb-3">
                <h3>Flight Path: {{ @$main_flight->segments[0]->origin->city_name }} → {{ @$main_flight->segments[0]->destination->city_name }}</h3>
                
                <table class="info-table">
                    <tr>
                        <th>PNR</th>
                        <td class="bold">{{ @$main_flight->pnr }}</td>
                    </tr>
                    <tr>
                        <th>Departure</th>
                        <td>{{ @$main_flight->departure_time }}</td>
                    </tr>
                    <tr>
                        <th>Arrival</th>
                        <td>{{ @$main_flight->arrival_time }}</td>
                    </tr>
                    <tr>
                        <th>Flight Type</th>
                        <td>{{ @$main_flight->is_direct ? 'Direct' : 'Connecting' }}</td>
                    </tr>
                    <tr>
                        <th>Flight Class</th>
                        <td>{{ @$main_flight->flight_class == 1 ? 'Economy' : (@$main_flight->flight_class == 2 ? 'Business' : 'First') }}</td>
                    </tr>
                    <tr>
                        <th>Refundable</th>
                        <td>{{ @$main_flight->is_refundable ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Flight Status</th>
                        <td>
                            @if($main_flight->status == 1)
                                <span class="status-badge status-confirmed">Confirmed</span>
                            @elseif($main_flight->status == 2)
                                <span class="status-badge status-pending">Cancellation Pending</span>
                            @elseif($main_flight->status == 3)
                                <span class="status-badge status-cancelled">
                                    {{ $main_flight->is_refundable == 1 ? 'Refunded' : 'Cancelled' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Flight Price</th>
                        <td class="bold">{{ number_format(@$main_flight->total_price, 2) }} SAR</td>
                    </tr>
                </table>

                <!-- Flight Segments -->
                @if($main_flight->segments && count($main_flight->segments) > 0)
                <h4>Flight Segments</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Airline</th>
                            <th>Flight Number</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($main_flight->segments as $segment)
                        <tr>
                            <td>{{ @$segment->airline_name }}</td>
                            <td>{{ @$segment->flight_number }}</td>
                            <td>{{ @$segment->origin->city_name }} ({{ @$segment->origin->airport_code }})</td>
                            <td>{{ @$segment->destination->city_name }} ({{ @$segment->destination->airport_code }})</td>
                            <td>{{ @$segment->departure_time }}</td>
                            <td>{{ @$segment->arrival_time }}</td>
                            <td>{{ @$segment->duration }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <!-- Price Summary -->
    <div class="section">
        <h2>Price Summary</h2>
        <div class="price-box">
            <p class="bold">Total Trip Cost</p>
            <p class="price-amount">{{ number_format($reservation->price, 2) }} {{ $reservation->currency }}</p>
            @if($reservation->payment_method == 1)
                <p class="note">✓ Payment Completed on {{ $reservation->paid_at ? $reservation->paid_at->format('d M Y, H:i') : $reservation->created_at->format('d M Y, H:i') }}</p>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="section">
        <p class="note text-center">
            This document was generated on {{ now()->format('d M Y, H:i') }}<br>
            For any inquiries, please contact our customer service with your booking reference: {{ $reservation->uuid }}
        </p>
    </div>
</div>
</body>
</html>
