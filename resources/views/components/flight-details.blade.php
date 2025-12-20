<div class="plan-details flight-details">
    <div class="row">
        <div class="col-md-12">
            @if ($flightSegment)
                @foreach ($flightSegment as $key => $flight)
                    <h3 class="text-dark mb-4">
                        {{ __('dashboard.Flight Type') }} : <span
                            class="text-warning">{{ $key == 0 ? __('dashboard.inboard') : __('dashboard.outboard') }}</span>
                    </h3>
                    <div class="airway-details">
                        @foreach ($flight as $segment)
                            <div class="flight-time">
                                <div class="time">
                                    {{ $segment['Origin']['Airport']['AirportName'] }}
                                </div>
                                <div class="time-code">
                                    {{ \Carbon\Carbon::parse($segment['Origin']['DepTime'])->format('d-m-Y H:i A') }}
                                </div>
                            </div>
                            <div class="flight-road">
                                <div class="road">
                                                                                    <span>{{ $segment['Duration'] }}
                                                                                        {{ __('dashboard.Min') }}</span>
                                    <hr class="my-2">
                                    <span>{{ $segment['Airline']['AirlineName'] }}</span>
                                    @if(isset($segment['Airline']['AirlineCode']) && isset($segment['Airline']['FlightNumber']))
                                        <br><small class="text-muted">{{ __('dashboard.Flight') }}: {{ $segment['Airline']['AirlineCode'] }}{{ $segment['Airline']['FlightNumber'] }}</small>
                                    @elseif(isset($segment['FlightNumber']))
                                        <br><small class="text-muted">{{ __('dashboard.Flight') }}: {{ $segment['FlightNumber'] }}</small>
                                    @endif
                                </div>
                                <i class="fas fa-plane"></i>
                            </div>
                            <div class="flight-time">
                                <div class="time">
                                    {{ $segment['Destination']['Airport']['AirportName'] }}
                                </div>
                                <div class="time-code">
                                    {{ \Carbon\Carbon::parse($segment['Destination']['ArrTime'])->format('d-m-Y H:i A') }}
                                </div>
                            </div>
                        @endforeach
                        <div class="plan-details">
                            <div class="plan-details-title">
                                <h6 style="font-size: 12px;">
                                    {{ __('dashboard.Flight Class') }} :
                                    <span class="text-warning">
                                                                                        @if ($flight[0]['CabinClass'] == 2)
                                            {{ __('dashboard.Economy') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 3)
                                            {{ __('dashboard.PremiumEconomy') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 4)
                                            {{ __('dashboard.Business') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 5)
                                            {{ __('dashboard.PremiumBusiness') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 6)
                                            {{ __('dashboard.First Class') }}
                                        @endif

                                                                                    </span>
                                </h6>
                            </div>
                            <div class="plan-details-title"
                                 style="font-size: 12px;">
                                <h6 style="font-size: 12px;">
                                    {{ __('dashboard.Bags') }} :
                                    <span class="text-warning"
                                          style="font-size: 12px;">
                                                                                        <span
                                                                                            class="text-success">{{ __('dashboard.bag_weight') }}:</span>
                                                                                        {{ $flight[0]['Baggage'] }}</span>
                                    <br>
                                    <span class="text-warning"
                                          style="font-size: 12px;">
                                                                                        <span
                                                                                            class="text-success">{{ __('dashboard.bag_hand') }}:</span>
                                                                                        {{ $flight[0]['CabinBaggage'] }}</span>
                                </h6>
                            </div>

                        </div>
                    </div>
                    {{-- <hr> --}}
                @endforeach
            @endif
        </div>

        <div class="col-md-12">
            @if (session()->has('flight.inbound_flightSegment'))
                @foreach (session('flight.inbound_flightSegment') as $key => $flight)
                    <h3 class="text-dark mb-4">
                        {{ __('dashboard.Flight Type') }} : <span
                            class="text-warning">{{ __('dashboard.outboard') }}</span>
                    </h3>
                    <div class="airway-details">
                        @foreach ($flight as $segment)
                            <div class="flight-time">
                                <div class="time">
                                    {{ $segment['Origin']['Airport']['AirportName'] }}
                                </div>
                                <div class="time-code">
                                    {{ \Carbon\Carbon::parse($segment['Origin']['DepTime'])->format('d-m-Y H:i A') }}
                                </div>
                            </div>
                            <div class="flight-road">
                                <div class="road">
                                                                                    <span>{{ $segment['Duration'] }}
                                                                                        {{ __('dashboard.Min') }}</span>
                                    <hr class="my-2">
                                    <span>{{ $segment['Airline']['AirlineName'] }}</span>
                                    @if(isset($segment['Airline']['AirlineCode']) && isset($segment['Airline']['FlightNumber']))
                                        <br><small class="text-muted">{{ __('dashboard.Flight') }}: {{ $segment['Airline']['AirlineCode'] }}{{ $segment['Airline']['FlightNumber'] }}</small>
                                    @elseif(isset($segment['FlightNumber']))
                                        <br><small class="text-muted">{{ __('dashboard.Flight') }}: {{ $segment['FlightNumber'] }}</small>
                                    @endif
                                </div>
                                <i class="fas fa-plane"></i>
                            </div>
                            <div class="flight-time">
                                <div class="time">
                                    {{ $segment['Destination']['Airport']['AirportName'] }}
                                </div>
                                <div class="time-code">
                                    {{ \Carbon\Carbon::parse($segment['Destination']['ArrTime'])->format('d-m-Y H:i A') }}
                                </div>
                            </div>
                        @endforeach
                        <div class="plan-details">
                            <div class="plan-details-title">
                                <h6 style="font-size: 12px;">
                                    {{ __('dashboard.Flight Class') }} :
                                    <span class="text-warning">
                                                                                        @if ($flight[0]['CabinClass'] == 2)
                                            {{ __('dashboard.Economy') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 3)
                                            {{ __('dashboard.PremiumEconomy') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 4)
                                            {{ __('dashboard.Business') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 5)
                                            {{ __('dashboard.PremiumBusiness') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 6)
                                            {{ __('dashboard.First Class') }}
                                        @endif

                                                                                    </span>
                                </h6>
                            </div>
                            <div class="plan-details-title"
                                 style="font-size: 12px;">
                                <h6 style="font-size: 12px;">
                                    {{ __('dashboard.Bags') }} :
                                    <span class="text-warning"
                                          style="font-size: 12px;">{{ $flight[0]['Baggage'] }}</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                    {{-- <hr> --}}
                @endforeach
            @endif
        </div>

        <div class="col-md-12">
            @if (session()->has('flight.inbound_flightSegment'))
                @foreach (session('flight.inbound_flightSegment') as $key => $flight)
                    <h3 class="text-dark mb-4">
                        {{ __('dashboard.Flight Type') }} : <span
                            class="text-warning">{{ __('dashboard.outboard') }}</span>
                    </h3>
                    <div class="airway-details">
                        @foreach ($flight as $segment)
                            <div class="flight-time">
                                <div class="time">
                                    {{ $segment['Origin']['Airport']['AirportName'] }}
                                </div>
                                <div class="time-code">
                                    {{ \Carbon\Carbon::parse($segment['Origin']['DepTime'])->format('d-m-Y H:i A') }}
                                </div>
                            </div>
                            <div class="flight-road">
                                <div class="road">
                                    <span>{{ $segment['Duration'] }}
                                        {{ __('dashboard.Min') }}</span>
                                    <hr class="my-2">
                                    <span>{{ $segment['Airline']['AirlineName'] }}</span>
                                </div>
                                <i class="fas fa-plane"></i>
                            </div>
                            <div class="flight-time">
                                <div class="time">
                                    {{ $segment['Destination']['Airport']['AirportName'] }}
                                </div>
                                <div class="time-code">
                                    {{ \Carbon\Carbon::parse($segment['Destination']['ArrTime'])->format('d-m-Y H:i A') }}
                                </div>
                            </div>
                        @endforeach
                        <div class="plan-details">
                            <div class="plan-details-title">
                                <h6 style="font-size: 12px;">
                                    {{ __('dashboard.Flight Class') }} :
                                    <span class="text-warning">
                                        @if ($flight[0]['CabinClass'] == 2)
                                            {{ __('dashboard.Economy') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 3)
                                            {{ __('dashboard.PremiumEconomy') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 4)
                                            {{ __('dashboard.Business') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 5)
                                            {{ __('dashboard.PremiumBusiness') }}
                                        @endif

                                        @if ($flight[0]['CabinClass'] == 6)
                                            {{ __('dashboard.First Class') }}
                                        @endif
                                    </span>
                                </h6>
                            </div>
                            <div class="plan-details-title"
                                 style="font-size: 12px;">
                                <h6 style="font-size: 12px;">
                                    {{ __('dashboard.Bags') }} :
                                    <span class="text-warning"
                                          style="font-size: 12px;">{{ $flight[0]['Baggage'] }}</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                    {{-- <hr> --}}
                @endforeach
            @endif
        </div>
        
        {{-- Flight Cancellation Policy Section - Moved outside to always show --}}
        <div class="col-md-12">
            @if(session('outbound_flight') || session('inbound_flight'))
                <div class="mt-4">
                    <h4 class="text-dark">{{ __('dashboard.Cancellation Policy') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
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
                                        @php
                                            $outbound = session('outbound_flight');
                                            $inbound = session('inbound_flight');
                                            $is_refundable = false;
                                            
                                            if($outbound && isset($outbound['IsRefundable'])) {
                                                $is_refundable = $outbound['IsRefundable'];
                                            } elseif($inbound && isset($inbound['IsRefundable'])) {
                                                $is_refundable = $inbound['IsRefundable'];
                                            }
                                        @endphp
                                        
                                        @if($is_refundable)
                                            <span class="badge bg-success">{{ __('dashboard.Yes') }}</span>
                                            <small class="d-block mt-1 text-muted">{{ __('dashboard.Refund with applicable charges') }}</small>
                                        @else
                                            <span class="badge bg-danger">{{ __('dashboard.No') }}</span>
                                            <small class="d-block mt-1 text-muted">{{ __('dashboard.Non-refundable ticket') }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('dashboard.Ticket Type') }}</strong></td>
                                    <td>
                                        @php
                                            $is_lcc = false;
                                            if($outbound && isset($outbound['IsLCC'])) {
                                                $is_lcc = $outbound['IsLCC'];
                                            } elseif($inbound && isset($inbound['IsLCC'])) {
                                                $is_lcc = $inbound['IsLCC'];
                                            }
                                        @endphp
                                        
                                        @if($is_lcc)
                                            {{ __('dashboard.Low Cost Carrier') }} (LCC)
                                            <small class="d-block mt-1 text-muted">{{ __('dashboard.Restrictions may apply') }}</small>
                                        @else
                                            {{ __('dashboard.Full Service Carrier') }}
                                            <small class="d-block mt-1 text-muted">{{ __('dashboard.Standard airline policies apply') }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('dashboard.Cancellation Terms') }}</strong></td>
                                    <td>
                                        @if($is_refundable)
                                            {{ __('dashboard.Cancellation allowed with applicable charges') }}<br>
                                            <small class="text-muted">{{ __('dashboard.Processing time: 7-14 business days') }}</small>
                                        @else
                                            {{ __('dashboard.No cancellation allowed') }}<br>
                                            <small class="text-muted">{{ __('dashboard.Changes subject to airline policy') }}</small>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $last_ticket_date = null;
                                    if($outbound && isset($outbound['LastTicketDate'])) {
                                        $last_ticket_date = $outbound['LastTicketDate'];
                                    } elseif($inbound && isset($inbound['LastTicketDate'])) {
                                        $last_ticket_date = $inbound['LastTicketDate'];
                                    }
                                @endphp
                                @if($last_ticket_date)
                                <tr>
                                    <td><strong>{{ __('dashboard.Last Ticketing Date') }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($last_ticket_date)->format('d M Y, H:i') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
