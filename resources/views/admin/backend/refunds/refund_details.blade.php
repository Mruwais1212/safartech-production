<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#return_details{{ $reservation->id }}">
    {{ __('dashboard.refund Details') }}
</a>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="return_details{{ $reservation->id }}"
    id="return_details{{ $reservation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-center" id="exampleModalLabell">
                    {{ __('dashboard.refund Details') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="text-align:center;margin: 14px;">
                    <div class="tab-content">
                        <div id="booking_information{{ $reservation->id }}" class="tab-pane fade in active">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.total') }}</th>
                                                <th>{{ __('dashboard.refund_type') }}</th>
                                                <th>{{ __('dashboard.refund_amount') }}</th>
                                                <th>{{ __('dashboard.refund') }}</th>
                                            </tr>
                                        </thead>
                                        @if($type == 'hotels')
                                        <tbody>
                                            <tr>
                                                <td>{{ $reservation->hotel->price_with_tax }} {{ __('dashboard.sar') }}</td>
                                                <td>{{ __('dashboard.'.$reservation->hotel->chargeType) }}</td>
                                                <td>50</td>
                                                <td>{{ $reservation->hotel->price_with_tax / 2 }} {{ __('dashboard.sar') }}</td>
                                            </tr>
                                        </tbody>
                                        @endif
                                        @if($type == 'flights')
                                        <tbody>
                                            <tr>
                                                <td>{{ $reservation->flight->price_with_tax }} {{ __('dashboard.sar') }}</td>
                                                <td></td>
                                                <td>50</td>
                                                <td>{{ $reservation->flight->price_with_tax / 2 }} {{ __('dashboard.sar') }}</td>
                                            </tr>
                                        </tbody>
                                        @endif

                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                    class="btn btn-secondary"data-dismiss="modal">{{ __('dashboard.close') }}</button>
            </div>
        </div>
    </div>
</div>
