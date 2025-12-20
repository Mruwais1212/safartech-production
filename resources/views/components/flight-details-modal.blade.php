<div class="plan-details flight-details">
    <div class="row">
        <div class="col-md-12">
            @if ($segments)
                @foreach ($segments as $key => $flight)
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
                    <hr>
                @endforeach
            @endif
        </div>
    </div>
</div>
