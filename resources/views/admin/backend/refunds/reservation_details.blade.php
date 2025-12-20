<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#reservation_details{{ $reservation->id }}">
    {{ __('dashboard.Reservation Details') }}
</a>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="reservation_details{{ $reservation->id }}"
    id="reservation_details{{ $reservation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-center" id="exampleModalLabel">
                    {{ __('dashboard.Reservation Details') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="text-align:center;margin: 14px;">
                    <ul class="nav nav-pills nav-fill" role="tablist">
                        <li class="nav-item border-radius active">
                            <a data-toggle="tab" href="#user_information{{ $reservation->id }}" class="nav-link"
                                style="border-radius: 20px">{{ __('dashboard.user_information') }}</a>
                        </li>
                        @if ($reservation->hotel)
                            <li class="nav-item border-radius">
                                <a class="nav-link" data-toggle="tab" href="#hotel_information{{ $reservation->id }}"
                                    style="border-radius: 20px">{{ __('dashboard.hotel_information') }}</a>
                            </li>
                        @endif
                        @if ($reservation->flight->count() > 0)
                            <li class="nav-item border-radius">
                                <a class="nav-link" data-toggle="tab" href="#flight_information{{ $reservation->id }}"
                                    style="border-radius: 20px">{{ __('dashboard.flight_information') }}</a>
                            </li>
                        @endif

                        <li class="nav-item border-radius">
                            <a class="nav-link" data-toggle="tab" href="#passenger_details{{ $reservation->id }}"
                                style="border-radius: 20px">{{ __('dashboard.passengers_details') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="user_information{{ $reservation->id }}" class="tab-pane fade in active">
                            <div class="row">
                                <x-input-details class="border-radius" formClass="col-lg-6"
                                    placeholder="{{ __('dashboard.client_name') }}"
                                    value="{{ @$reservation->user->name }}" type="text">
                                </x-input-details>
                                <x-input-details class="border-radius" formClass="col-lg-6"
                                    placeholder="{{ __('dashboard.client_phone') }}"
                                    value="{{ $reservation->user->phone }}" type="text">
                                </x-input-details>
                            </div>

                            <div class="row">
                                <x-input-details class="border-radius" formClass="col-lg-6"
                                    placeholder="{{ __('dashboard.client_email') }}"
                                    value="{{ @$reservation->user->email }}" type="text">
                                </x-input-details>
                                <div class="form-group col-lg-6">
                                    <label
                                        class="control-label col-lg-12 text-{{ __('dashboard.right') }}">{{ __('dashboard.client_photo') }}</label>
                                    <div class="col-lg-12">
                                        <img class="border-radius img-responsive" formClass="col-lg-6" height="75px"
                                            width="100px"
                                            src="{{ file_exists(asset('uploads/' . $reservation->user->photo)) ? asset($reservation->user->photo) : asset('images/default_user.png') }}"
                                            alt="" style="width: 100px; height: 100px">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (@$reservation->hotel)
                            <div id="hotel_information{{ $reservation->id }}" class="tab-pane fade">
                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.hotel_name')" formClass="col-lg-12"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->hotel->hotel_name }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.hotel_address')" formClass="col-lg-12"
                                        type="text" value="{{ @$reservation->hotel->hotel_address }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.checking_in_date')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->hotel->check_in }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.checking_out_date')" formClass="col-lg-6"
                                        type="text" value="{{ @$reservation->hotel->check_out }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.price')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->hotel->price }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.currency')" formClass="col-lg-6"
                                        type="text" value="{{ @$reservation->hotel->currency }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.rooms')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->hotel->rooms }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.adults')" formClass="col-lg-6"
                                        type="text" value="{{ @$reservation->hotel->adults }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.booking_code')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->hotel->booking_code }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.confirmation_number')" formClass="col-lg-6"
                                        type="text" value="{{ @$reservation->hotel->confirmation_number }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <label
                                            class="control-label col-lg-12 text-{{ __('dashboard.right') }}">{{ __('dashboard.hotel_photo') }}</label>
                                        <div class="col-lg-12">
                                            <img class="border-radius img-responsive" formClass="col-lg-12"
                                                height="100%" width="100%"
                                                src="{{ $reservation->hotel->hotel_image }}" alt=""
                                                style="width: 100%; height: 100%; max-height: 300px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div id="flight_information{{ $reservation->id }}" class="tab-pane fade">
                            @if (@$reservation->flight[0])
                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.flight_from')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->segments[0]->origin->city_name }} ({{ @$reservation->flight[0]->segments[0]->origin->country_name }})">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.flight_to')" formClass="col-lg-6"
                                        type="text"
                                        value="{{ @$reservation->flight[0]->segments[0]->destination->city_name }} ({{ @$reservation->flight[0]->segments[0]->destination->country_name }})">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.airline_name')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->segments[0]->airline->airline_name }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.is_direct')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->is_direct ? __('dashboard.yes') : __('dashboard.no') }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.departure_time')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->segments[0]->origin->dep_time }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.arrival_time')" formClass="col-lg-6"
                                        type="text"
                                        value="{{ @$reservation->flight[0]->segments[0]->destination->dep_time }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.duration')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->segments[0]->duration }} {{ __('dashboard.minutes') }}">
                                    </x-input-details>

                                    <x-input-details class="border-radius" :placeholder="__('dashboard.flight_number')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->segments[0]->airline->flight_number }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.flight_class')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->getClassFlight() }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.flight_price')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->total_price }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.is_low_cost')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->is_lcc ? __('dashboard.yes') : __('dashboard.no') }}">
                                    </x-input-details>

                                    <x-input-details class="border-radius" :placeholder="__('dashboard.is_refundable')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->is_refundable ? __('dashboard.yes') : __('dashboard.no') }}">
                                    </x-input-details>
                                </div>

                                <div class="row">
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.pnr')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->pnr }}">
                                    </x-input-details>
                                    <x-input-details class="border-radius" :placeholder="__('dashboard.result_index')" formClass="col-lg-6"
                                        type="text" class="border-radius"
                                        value="{{ @$reservation->flight[0]->result_index }}">
                                    </x-input-details>
                                </div>
                            @endif
                        </div>

                        <div id="passenger_details{{ $reservation->id }}" class="tab-pane fade">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>{{ __('dashboard.name') }}</th>
                                                <th>{{ __('dashboard.birth_date') }}</th>
                                                <th>{{ __('dashboard.gender') }}</th>
                                                <th>{{ __('dashboard.nationality') }}</th>
                                                <th>{{ __('dashboard.email') }}</th>
                                                <th>{{ __('dashboard.phone') }}</th>
                                                <th>{{ __('dashboard.address') }}</th>
                                                <th>{{ __('dashboard.passport_expiry_date') }}</th>
                                                <th>{{ __('dashboard.passport_number') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reservation->passengers as $passenger)
                                                <tr>
                                                    <td>{{ $passenger->title }} {{ $passenger->full_name }}</td>
                                                    <td>{{ $passenger->birth_date }}</td>
                                                    <td>{{ $passenger->gender == 1 ? __('dashboard.male') : __('dashboard.female') }}</td>
                                                    <td>{{ $passenger->getNationality->name_en }}</td>
                                                    <td>{{ $passenger->email }}</td>
                                                    <td>{{ $passenger->phone }}</td>
                                                    <td>{{ $passenger->address }}</td>
                                                    <td>{{ $passenger->passport_expiry_date }}</td>
                                                    <td>{{ $passenger->passport_number }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
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
