<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>e-Ticket</title>
    <style>
        @page {
            margin: 10px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #000;
            line-height: 1.6;
            margin: 0;
        }

        .container {
            padding: 10px;
            border: 1px solid #ccc;
        }

        h2, h3 {
            color: #003399;
            margin-bottom: 10px;
        }

        .logo {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .logo img {
            height: 60px;
        }

        .logo > div {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }

        .ticket-title {
            text-align: right;
        }

        .section {
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background-color: #f0f8ff;
        }

        th, td {
            border: 1px solid #999;
            padding: 8px;
            font-size: 12px;
            vertical-align: top;
            word-break: break-word;
        }

        th {
            background-color: #e6f0ff;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
        }

        .bold {
            font-weight: bold;
        }

        .note {
            font-style: italic;
            font-size: 11px;
            color: #555;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mb-1 {
            margin-bottom: 8px;
        }

        .mb-2 {
            margin-bottom: 15px;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <img src="{{public_path('/site/img/logo.png')}}" alt="Safartech Logo">
        <div class="ticket-title">
            <h2>e-ticket</h2>
            <p><strong>Booking reference #</strong> {{$reservation->uuid}}</p>
        </div>
    </div>
@foreach($reservation->flights as $flight)
    @php
    $booking = $flight->booking_json ? @json_decode($flight->booking_json) : null;
    $ticket_details = $flight->ticket_json ? @json_decode($flight->ticket_json) : null;
    $flight_details = $booking ? $booking->Itinerary : '';
    
    // Try to get ticket number from various sources
    $ticket_number = '';
    
    // Try ticket_json first (most specific)
    if ($ticket_details) {
        // Try different possible paths for ticket number in TBO response
        if (isset($ticket_details->FlightItinerary)) {
            if (isset($ticket_details->FlightItinerary->Passenger)) {
                $passengers = is_array($ticket_details->FlightItinerary->Passenger) 
                    ? $ticket_details->FlightItinerary->Passenger 
                    : [$ticket_details->FlightItinerary->Passenger];
                foreach ($passengers as $passenger) {
                    if (isset($passenger->Ticket) && isset($passenger->Ticket->TicketNumber)) {
                        $ticket_number = $passenger->Ticket->TicketNumber;
                        break;
                    }
                }
            }
            // Try other possible paths
            if (!$ticket_number && isset($ticket_details->FlightItinerary->Ticket) && isset($ticket_details->FlightItinerary->Ticket->TicketNumber)) {
                $ticket_number = $ticket_details->FlightItinerary->Ticket->TicketNumber;
            }
        }
        // Try direct ticket number path
        if (!$ticket_number && isset($ticket_details->TicketNumber)) {
            $ticket_number = $ticket_details->TicketNumber;
        }
        // Try PNR in ticket details
        if (!$ticket_number && isset($ticket_details->PNR)) {
            $ticket_number = $ticket_details->PNR;
        }
    }
    
    // Try booking_json as fallback
    if (!$ticket_number && $flight_details && isset($flight_details->Ticket) && isset($flight_details->Ticket->TicketNumber)) {
        $ticket_number = $flight_details->Ticket->TicketNumber;
    }
    
    // Use PNR as fallback (this is the most reliable identifier)
    if (!$ticket_number && $flight->pnr) {
        $ticket_number = $flight->pnr;
    }
    
    // Final fallback
    if (!$ticket_number) {
        $ticket_number = 'Not Available';
    }
    @endphp
    <div class="section">
        <table>
            <tr>
                <th>Passenger Name</th>
                <th>Ticket Number</th>
                <th>Information / Service request</th>
                <th>Type</th>
                <th>Contact</th>
                <th>Passport Number</th>
            </tr>
            @if($reservation->passengers && count($reservation->passengers) > 0)
                @foreach($reservation->passengers as $passenger)
            <tr>
                <td>
                    @php
                        $title_text = '';
                        if($passenger->title == 1) {
                            $title_text = 'Mr';
                        } elseif($passenger->title == 2) {
                            $title_text = 'Mrs';
                        } else {
                            $title_text = $passenger->title; // In case it's already a string
                        }
                    @endphp
                    {{$title_text}}. {{$passenger->first_name .' '.$passenger->last_name}}
                </td>
                <td>{{$ticket_number}}</td>
                <td>-</td>
                <td>
                @php
                    $birthdate = $passenger->birth_date;
    $today = new DateTime();
    $dob = new DateTime($birthdate);
    $age = $today->diff($dob)->y;

    $isAdult = $age >= 18;

    echo $isAdult ? 'Adult' : 'child';
                @endphp
                </td>
                <td>{{$passenger->phone}}</td>
                <td>{{$passenger->passport_number}}</td>
            </tr>
                @endforeach
            @else
            <tr>
                <td colspan="6" style="text-align: center;">No passenger information available</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <h3>Travel Itinerary</h3>
        <table>
            <tr>
                <th>Flight</th>
                <th>Check-in</th>
                <th>From</th>
                <th>To</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Departure terminal</th>
                <th>Arrival terminal</th>
                <th>Cabin</th>
                <th>Status</th>
                <th>Note</th>
            </tr>
            @if($flight->segments && count($flight->segments) > 0)
                @foreach($flight->segments as $segment)
                <tr>
                    <td>
                        @php
                            // Try to get flight number from various sources
                            $flight_number = '';
                            
                            // Try segment airline data first
                            if ($segment->airline && $segment->airline->airline_code && $segment->airline->flight_number) {
                                $flight_number = $segment->airline->airline_code . $segment->airline->flight_number;
                            }
                            // Try direct flight number
                            elseif (isset($segment->flight_number)) {
                                $flight_number = $segment->flight_number;
                            }
                            // Try airline code + flight number separately
                            elseif (isset($segment->airline_code) && isset($segment->flight_number)) {
                                $flight_number = $segment->airline_code . $segment->flight_number;
                            }
                            // Fallback
                            else {
                                $flight_number = 'N/A';
                            }
                        @endphp
                        {{ $flight_number }}
                    </td>
                    <td>{{ $segment->origin && $segment->origin->dep_time ? \Carbon\Carbon::parse($segment->origin->dep_time)->subHour()->format('H:i') : '02:00' }}</td>
                    <td>{{$segment->origin->city_name ?? ''}} ({{$segment->origin->airport_code ?? ''}})</td>
                    <td>{{$segment->destination->city_name ?? ''}} ({{$segment->destination->airport_code ?? ''}})</td>
                    <td>{{ $segment->origin && $segment->origin->dep_time ? \Carbon\Carbon::parse($segment->origin->dep_time)->format('d-M-y H:i') : '' }}</td>
                    <td>{{ $segment->destination && $segment->destination->arr_time ? \Carbon\Carbon::parse($segment->destination->arr_time)->format('d-M-y H:i') : '' }}</td>
                    <td>{{ $segment->origin->airport_code ? '1' : '-' }}</td>
                    <td>{{ $segment->destination->airport_code ? '1' : '-' }}</td>
                    <td>{{ ucfirst(strtolower($segment->cabin_class ?? 'Economy')) }} class</td>
                    <td>OK</td>
                    <td>{{ count($flight->segments) == 1 ? 'Non stop' : 'Connecting' }}</td>
                </tr>
                @endforeach
            @else
            <tr>
                <td>
                    @php
                        // Try to get flight number from flight data
                        $flight_number = '';
                        
                        // Try PNR as flight identifier
                        if ($flight->pnr) {
                            $flight_number = $flight->pnr;
                        }
                        // Try flight number if available
                        elseif (isset($flight->flight_number)) {
                            $flight_number = $flight->flight_number;
                        }
                        // Fallback
                        else {
                            $flight_number = 'N/A';
                        }
                    @endphp
                    {{ $flight_number }}
                </td>
                <td>02:00</td>
                <td>{{$flight->flight_from ?? 'N/A'}}</td>
                <td>{{$flight->flight_to ?? 'N/A'}}</td>
                <td>{{ $flight->departure_time ? \Carbon\Carbon::parse($flight->departure_time)->format('d-M-y H:i') : 'N/A' }}</td>
                <td>{{ $flight->arrival_time ? \Carbon\Carbon::parse($flight->arrival_time)->format('d-M-y H:i') : 'N/A' }}</td>
                <td>1</td>
                <td>1</td>
                <td>{{ ucfirst(strtolower($flight->flight_class ?? 'Economy')) }} class</td>
                <td>OK</td>
                <td>{{ $flight->is_direct ? 'Non stop' : 'Connecting' }}</td>
            </tr>
            @endif
        </table>
        <p class="note">Please note that the check-in will be closed one hour before the departure.</p>
    </div>

    <div class="section">
        <h3>Fare Details</h3>
        <table>
            <tr>
                <th>Trip segment</th>
                <th>Fare basis</th>
                <th>Type</th>
                <th>Passenger Name</th>
                <th>Baggage allowance</th>
                <th>Excess bag paid</th>
                <th>Seat</th>
                <th>Hand baggage allowance</th>
                <th>Currency</th>
                <th>Base fare</th>
                <th>Tax & surcharges</th>
                <th>Ticket fare</th>
            </tr>
            @if($reservation->passengers && count($reservation->passengers) > 0)
                @foreach($reservation->passengers as $passenger)
                @php
                    $segment_route = $flight->flight_from && $flight->flight_to ? 
                        $flight->flight_from . ' → ' . $flight->flight_to : 
                        ($flight->segments->first() ? 
                            $flight->segments->first()->origin->airport_code . ' → ' . $flight->segments->last()->destination->airport_code : 
                            'N/A → N/A');
                    
                    $fare_class = $flight->segments->first() ? $flight->segments->first()->airline->fare_class ?? 'Y' : 'Y';
                    $baggage = $flight->segments->first() ? $flight->segments->first()->baggage ?? '23 Kg' : '23 Kg';
                    $cabin_baggage = $flight->segments->first() ? $flight->segments->first()->cabin_baggage ?? '7kg' : '7kg';
                    
                    // Calculate individual passenger price
                    $passenger_count = count($reservation->passengers);
                    $base_fare = $passenger_count > 0 ? number_format($flight->price_without_tax / $passenger_count, 2) : number_format($flight->price_without_tax ?? 0, 2);
                    $tax_amount = $passenger_count > 0 ? number_format($flight->tax_amount / $passenger_count, 2) : number_format($flight->tax_amount ?? 0, 2);
                    $total_fare = $passenger_count > 0 ? number_format($flight->total_price / $passenger_count, 2) : number_format($flight->total_price ?? 0, 2);
                @endphp
            <tr>
                <td>{{ $segment_route }}</td>
                <td>{{ $fare_class }}</td>
                <td>
                    @php
                        $birthdate = $passenger->birth_date;
                        $today = new DateTime();
                        $dob = new DateTime($birthdate);
                        $age = $today->diff($dob)->y;
                        $isAdult = $age >= 18;
                        echo $isAdult ? 'Adult' : 'Child';
                    @endphp
                </td>
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
                    {{$title_text}}. {{$passenger->first_name .' '.$passenger->last_name}}
                </td>
                <td>{{ $baggage }}</td>
                <td>0 Kg</td>
                <td>-</td>
                <td>{{ $cabin_baggage }}</td>
                <td>{{ $reservation->currency ?? 'SAR' }}</td>
                <td>{{ $base_fare }}</td>
                <td>{{ $tax_amount }}</td>
                <td>{{ $total_fare }}</td>
            </tr>
                @endforeach
            @else
            <tr>
                <td>{{ $flight->flight_from ?? 'N/A' }} → {{ $flight->flight_to ?? 'N/A' }}</td>
                <td>{{ $flight->segments->first() ? $flight->segments->first()->airline->fare_class ?? 'Y' : 'Y' }}</td>
                <td>Adult(s)</td>
                <td>No passenger information</td>
                <td>{{ $flight->segments->first() ? $flight->segments->first()->baggage ?? '23 Kg' : '23 Kg' }}</td>
                <td>0 Kg</td>
                <td>-</td>
                <td>{{ $flight->segments->first() ? $flight->segments->first()->cabin_baggage ?? '7kg' : '7kg' }}</td>
                <td>{{ $reservation->currency ?? 'SAR' }}</td>
                <td>{{ number_format($flight->price_without_tax ?? 0, 2) }}</td>
                <td>{{ number_format($flight->tax_amount ?? 0, 2) }}</td>
                <td>{{ number_format($flight->total_price ?? 0, 2) }}</td>
            </tr>
            @endif
        </table>
        <p class="bold">Ticket amount : {{ number_format($flight->total_price ?? 0, 2) }} {{ $reservation->currency ?? 'SAR' }}</p>
    </div>

    <div class="section">
        <h3>Fare Rules</h3>
        <table>
            <tr>
                <th>Trip segment</th>
                <th>Fare validity</th>
                <th>Type</th>
                <th>Change fees **</th>
                <th>Refund fees **</th>
            </tr>
            <tr>
                <td>{{ $flight->flight_from ?? 'N/A' }} → {{ $flight->flight_to ?? 'N/A' }}</td>
                <td>
                    @if($flight->departure_time)
                        Not valid before {{ \Carbon\Carbon::parse($flight->departure_time)->format('d-M-y') }}<br>
                        Not valid after {{ \Carbon\Carbon::parse($flight->departure_time)->addYear()->format('d-M-y') }}
                    @else
                        Validity information not available
                    @endif
                </td>
                <td>Adult(s)</td>
                <td>
                    @if($flight->is_refundable)
                        Change permitted with fees<br>
                        Additional penalty in case of no-show<br>
                        *Change to higher fare on same flight with fee
                    @else
                        Changes not permitted<br>
                        Ticket is non-changeable
                    @endif
                </td>
                <td>
                    @if($flight->is_refundable)
                        Refund permitted with penalties<br>
                        Cancellation charges may apply
                    @else
                        Non-refundable<br>
                        No refund permitted
                    @endif
                </td>
            </tr>
        </table>
        <p class="note">(*) Please note this is a total amount per passenger type.<br>(**) Please note that fare difference will be added to the above change fees if applicable.</p>
    </div>
    @endforeach
</div>
</body>
</html>
