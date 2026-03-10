@extends('website.layout')
@section('title', __('dashboard.hotel_details'))
@section('content')

    @php
        $hotel = json_decode(@$hotel);
        $location = $hotel->latitude . ',' . $hotel->longitude;
    @endphp

    <!-- Header Start -->
    <div style="background-image: url('/site/img/hotels.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.hotel_details') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">{{ __('dashboard.Refund Details') }}</h5>
                </div>
                <div class="modal-body">
                    <p id="refundMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dashboard.Cancel') }}</button>
                    <a id="confirmRoomSelection" href="#" class="btn btn-warning">{{ __('dashboard.Confirm Selection') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- steps form -->
    <div class="container hotel-single">
        <div class="row">
            <div class="col-md-12">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="plan-section mt-4">
                                <div class="hotel-buttons mb-4">
                                    <a href="/hotels" class="back-button">
                                        <i class="bi bi-arrow-left"></i>
                                    </a>
                                    {{-- <div class="options-buttons">
                                        <button type="button" class="fav">
                                            <i class="bi bi-suit-heart"></i>
                                            Save
                                        </button>
                                    </div> --}}
                                </div>
                                @if(count(collect(@$hotel->images)->take(5)->toArray()) > 0)
                                <div class="plan-gallery">
                                    <div class="container p-0">
                                        <div class="row justify-content-start">
                                            <div class="col col-md-12 gallery-container-wrap position-relative">
                                                <div class="gallery-container" id="gallery-dynamic-thumbnails">
                                                    @foreach (collect(@$hotel->images)->take(5)->toArray() as $key => $image)
                                                        <a class="gallery-item" data-index="{{ $key }}"
                                                            data-src="{{ $image }}">
                                                            <img alt="layers of blue." class="img-responsive"
                                                                src="{{ $image }}" />
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="hotel-details">
                                    <ul class="nav nav-tabs hotel-tabs" id="myTab">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="home-tab"
                                                href="#home">{{ __('dashboard.Overview') }}</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="profile-tab"
                                                href="#profile">{{ __('dashboard.Amenities') }}</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="room-tab"
                                                href="#room">{{ __('dashboard.Rooms') }}</a>
                                        </li>
                                    </ul>
                                    <div class="hotel-content-tabs">
                                        <div class="tab-pane fade show active" id="home">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h3>
                                                        {{ app()->getLocale() == 'ar' ? $hotel->name_ar : $hotel->name_en }}
                                                    </h3>
                                                    <div class="hotel-stars">
                                                        <input class="star-rate rating rating-loading" data-min="0"
                                                            data-max="5" data-step="1" value="{{ $hotel->rating }}"
                                                            data-size="xs" disabled="">
                                                    </div>
                                                    <p class="hotel-note">
                                                        {!! \App\Support\HtmlSanitizer::sanitizeLimitedHtml(app()->getLocale() == 'ar' ? $hotel->description_ar : $hotel->description_en) !!}
                                                    </p>
                                                    <div class="hotel-rate">
                                                        <span class="rate-box">{{ __('dashboard.hotel_rate') }}
                                                            :{{ $hotel->rating }}</span>
                                                    </div>
                                                    <div class="amenities">
                                                        <h6 class="title">{{ __('dashboard.Popular amenities') }}</h6>
                                                        <div class="list">
                                                            @foreach (collect(collect(app()->getLocale() == 'ar' ? $hotel->facilities_ar : $hotel->facilities_en))->take(10)->toArray() as $amenity)
                                                                <span> <i
                                                                        class="fas fa-check"></i>{{ $amenity }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <a href="#profile" onclick="$('#profile-tab').click()" class="see-all">
                                                        {{ __('dashboard.See all property amenities') }}
                                                        <i class="bi bi-chevron-right"></i>
                                                    </a>
                                                </div>

                                                <div class="col-md-4">
                                                    <h6 class="map-title">{{ __('dashboard.Explore the area') }}</h6>
                                                    <div class="map">
                                                        {{-- <img width="100%" height="150" src="/site/img/mapp.png"
                                                            alt="" /> --}}
                                                        <gmp-map center="{{ $location }}" zoom="17"
                                                            map-id="DEMO_MAP_ID" style="height: 400px"></gmp-map>
                                                        <span class="map-desc">{{ $hotel->address }}</span>
                                                        {{-- <a href="https://www.google.com/maps/@<?php echo $location; ?>,13z?entry=ttu&g_ep=EgoyMDI0MDkxNi4wIKXMDSoASAFQAw"
                                                            class="see-map">{{ __('dashboard.View In Map') }}</a> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade show active" id="profile">
                                            <div class="amenities">
                                                <h6 class="title">{{ __('dashboard.Popular amenities') }}</h6>
                                                <div class="list">
                                                    @foreach (collect(app()->getLocale() == 'ar' ? $hotel->facilities_ar : $hotel->facilities_en)->toArray() as $amenity)
                                                        <span> <i class="fas fa-check"></i>{{ $amenity }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade show active" id="room">
                                            <div class="check-time">
                                                <div class="check-in">
                                                    <i class="fas fa-level-down-alt"></i>
                                                    <p>{{ __('dashboard.Check-in time') }}</p>
                                                    <span>{{ @$hotel->check_in_time }}</span>
                                                </div>
                                                <div class="check-out">
                                                    <i class="fas fa-level-up-alt"></i>
                                                    <p>{{ __('dashboard.Check-out time') }}</p>
                                                    <span>{{ @$hotel->check_out_time }}</span>
                                                </div>
                                            </div>

                                            <div class="rooms search-page">
                                                <div class="rooms-filter">
                                                    <!-- Filter Controls -->
                                                    <div class="rooms-filter-header">
                                                        <h3>{{ __('dashboard.Rooms') }}</h3>
                                                    </div>
                                                    <!-- Filterable Items -->
                                                    <div class="hotels-cards rooms-cards">
                                                        <div class="row">
                                                            @forelse ($rooms as $room)
                                                                <div data-tags="one_bed" class="col-md-4">
                                                                    <div class="card">
                                                                        <div class="owl-carousel hotel-image-carousel">
                                                                            <div class="owl-carousel-item">
                                                                                <div class="img-container">
                                                                                    @if (@$hotel->images)
                                                                                        @foreach (collect(@$hotel->images)->random(1)->take(1)->toArray() as $key => $image)
                                                                                            <img
                                                                                                src="{{ $image }}" />
                                                                                        @endforeach
                                                                                    @else
                                                                                        <img src="/no-image.png" />    
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-content">
                                                                            <div class="room-content-sections">
                                                                                <ul style="list-style: none"
                                                                                    class="list-group list-group-flush list">
                                                                                    @foreach ($room['Name'] as $name)
                                                                                        <li class="ml-5">
                                                                                            <h2 style="justify-content: center"
                                                                                                class="mt-0 mb-2">
                                                                                                {{ $name }}
                                                                                            </h2>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>

                                                                                {{-- Room Inclusions Section --}}
                                                                                @if (@$room['Inclusion'])
                                                                                    <hr class="my-2">
                                                                                    <div class="collection-sections">
                                                                                        <div class="section-first">
                                                                                            <h6 class="inclusions-title" style="font-size: 14px; font-weight: bold; color: #333; margin-bottom: 10px;">
                                                                                                <i class="fas fa-check-circle" style="color: #28a745; margin-right: 0px 6px;"></i>
                                                                                                {{ __('dashboard.Room Inclusions') }}
                                                                                            </h6>
                                                                                            <div class="inclusions-container" style="display: flex; flex-wrap: wrap; gap: 8px;">
                                                                                                @foreach (explode(',', $room['Inclusion']) as $inclusion)
                                                                                                    @if(trim($inclusion))
                                                                                                        <div class="inclusion-item" style="display: flex; align-items: center; padding: 10px 14px; background: linear-gradient(135deg, #e8f5e8 0%, #f0f9f0 100%); border-radius: 8px; border: 1px solid #c8e6c9; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                                                                            <i class="fas fa-check" style="color: #28a745; font-size: 10px; margin: 0px 6px;"></i>
                                                                                                            <span style="font-size: 12px; color: #2e7d32; font-weight: 500; white-space: nowrap;">
                                                                                                                {{ trim($inclusion) }}
                                                                                                            </span>
                                                                                                        </div>
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @else
                                                                                    {{-- Placeholder for consistent spacing --}}
                                                                                    <div class="inclusion-placeholder mb-4">
                                                                                        {{-- <hr class="my-2"> --}}
                                                                                        <div class="collection-sections">
                                                                                            <div class="section-first">
                                                                                                <h6 class="inclusions-title" style="font-size: 14px; font-weight: bold; color: #666; margin-bottom: 10px;">
                                                                                                    <i class="fas fa-info-circle" style="color: #6c757d; margin-right: 6px;"></i>
                                                                                                    {{ __('dashboard.Room Inclusions') }}
                                                                                                </h6>
                                                                                                <div class="inclusions-container" style="display: flex; align-items: center; padding: 10px 14px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                                                                                                    <span style="font-size: 12px; color: #6c757d; font-style: italic;">
                                                                                                        {{ __('dashboard.No special inclusions available') }}
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif

                                                                                {{-- Meal Type Section --}}
                                                                                @if (@$room['MealType'])
                                                                                    <div class="collection-sections">
                                                                                        <div class="section-first">
                                                                                            <h6 class="meal-type-title" style="font-size: 14px; font-weight: bold; color: #333; margin-bottom: 10px;">
                                                                                                <i class="fas fa-utensils" style="color: #e67e22; margin-right: 6px;"></i>
                                                                                                {{ __('dashboard.Meal Plan') }}
                                                                                            </h6>
                                                                                            <div class="meal-type-container">
                                                                                                <div class="meal-type-item" style="display: inline-flex; align-items: center; padding: 10px 14px; background: linear-gradient(135deg, #fdf2e9 0%, #fdebd0 100%); border-radius: 8px; border: 1px solid #f39c12; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                                                                    @php
                                                                                                        $mealType = strtolower(@$room['MealType']);
                                                                                                        $mealIcon = 'fas fa-utensils';
                                                                                                        if (str_contains($mealType, 'breakfast')) $mealIcon = 'fas fa-coffee';
                                                                                                        elseif (str_contains($mealType, 'lunch')) $mealIcon = 'fas fa-hamburger';
                                                                                                        elseif (str_contains($mealType, 'dinner')) $mealIcon = 'fas fa-wine-glass-alt';
                                                                                                        elseif (str_contains($mealType, 'all inclusive')) $mealIcon = 'fas fa-star';
                                                                                                        elseif (str_contains($mealType, 'half board')) $mealIcon = 'fas fa-clock';
                                                                                                    @endphp
                                                                                                    <i class="{{ $mealIcon }}" style="color: #d68910; font-size: 10px; margin: 0px 6px;"></i>
                                                                                                    <span style="font-size: 12px; color: #b7950b; font-weight: 500; white-space: nowrap;">
                                                                                                        {{ str_replace('_', ' ', @$room['MealType']) }}
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @else
                                                                                    {{-- Placeholder for consistent spacing --}}
                                                                                    <div class="meal-type-placeholder">
                                                                                        <div class="collection-sections">
                                                                                            <div class="section-first">
                                                                                                <h6 class="meal-type-title" style="font-size: 14px; font-weight: bold; color: #666; margin-bottom: 10px;">
                                                                                                    <i class="fas fa-utensils" style="color: #6c757d; margin-right: 6px;"></i>
                                                                                                    {{ __('dashboard.Meal Plan') }}
                                                                                                </h6>
                                                                                                <div class="meal-type-container" style="display: flex; align-items: center; padding: 10px 14px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                                                                                                    <span style="font-size: 12px; color: #6c757d; font-style: italic;">
                                                                                                        {{ __('dashboard.Meal plan not specified') }}
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>

                                                                            @if (@$room['IsRefundable'])
                                                                                {{-- <hr class="my-2"> --}}
                                                                                <div class="collection-sections">
                                                                                    <div class="section-first">
                                                                                        <span
                                                                                            class="text-success offer-refund">
                                                                                            {{ __('dashboard.Fully Refundable') }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            {{-- <hr class="my-2"> --}}
                                                                            <div class="collection-sections w-100">
                                                                                <div class="section-first w-100">
                                                                                    @if (@$room['RoomPromotion'][0])
                                                                                        <h6 class="supplements-title" style="font-size: 14px; font-weight: bold; color: #333; margin-bottom: 8px;">
                                                                                            <i class="fas fa-tag" style="color: #009636; margin-right: 6px;"></i>
                                                                                            {{ __('dashboard.Promotions') }}
                                                                                        </h6>
                                                                                        <div class="hotel-rate justify-content-start" style="display: flex; flex-wrap: wrap; gap: 5px; ">
                                                                                            {{-- Display each promotion --}}
                                                                                            @foreach (explode(',', @$room['RoomPromotion'][0]) as $promo)
                                                                                                @if(trim($promo))
                                                                                                    <span class="rate-box" style="width: fit-content; white-space: nowrap;">{{ trim($promo) }}</span>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        {{-- Placeholder for consistent spacing --}}
                                                                                        <div class="promotion-placeholder">
                                                                                            <h6 class="supplements-title" style="font-size: 14px; font-weight: bold; color: #666; margin-bottom: 8px;">
                                                                                                <i class="fas fa-tag" style="color: #6c757d; margin-right: 6px;"></i>
                                                                                                {{ __('dashboard.Promotions') }}
                                                                                            </h6>
                                                                                            <div class="hotel-rate justify-content-start" style="display: flex; flex-wrap: wrap; gap: 5px;">
                                                                                                <span class="rate-box" style="width: fit-content; white-space: nowrap; background-color: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6;padding: 10px;">
                                                                                                    {{ __('dashboard.No promotions available') }}
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif

                                                                                    {{-- Display Supplements --}}
                                                                                    @if (@$room['Supplements'] && count(@$room['Supplements']) > 0)
                                                                                        <div class="supplements-section mt-3 w-100">
                                                                                            <h6 class="supplements-title" style="font-size: 14px; font-weight: bold; color: #333; margin-bottom: 8px;">
                                                                                                <i class="fas fa-plus-circle" style="color: #ffc107; margin-right: 6px;"></i>
                                                                                                {{ __('dashboard.Additional Charges') }}
                                                                                            </h6>
                                                                                            @foreach (@$room['Supplements'] as $supplementGroup)
                                                                                                @if(is_array($supplementGroup))
                                                                                                    @foreach ($supplementGroup as $supplement)
                                                                                                        <div class="supplement-item mb-2" style="display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; margin-bottom: 4px; background-color: #f8f9fa; border-radius: 8px; border-left: 3px solid #ffc107;">
                                                                                                            <div class="supplement-info">
                                                                                                                <span class="supplement-description" style="font-size: 12px; color: #495057; font-weight: 500;">
                                                                                                                    {{ ucfirst(str_replace('_', ' ', @$supplement['Description'])) }}
                                                                                                                    @if(@$supplement['Type'])
                                                                                                                        <small style="color: #6c757d;">({{ @$supplement['Type'] }})</small>
                                                                                                                    @endif
                                                                                                                </span>
                                                                                                            </div>
                                                                                                            <div class="supplement-price">
                                                                                                                <span class="price-amount" style="font-size: 13px; font-weight: bold; color: #dc3545;">
                                                                                                                    {{ @$supplement['Price'] }} {{ @$supplement['Currency'] }}
                                                                                                                </span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    @endforeach
                                                                                                     
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        {{-- Placeholder for consistent spacing --}}
                                                                                        <div class="supplements-placeholder">
                                                                                            <div class="supplements-section mt-3 w-100">
                                                                                                <h6 class="supplements-title" style="font-size: 14px; font-weight: bold; color: #666; margin-bottom: 8px;">
                                                                                                    <i class="fas fa-plus-circle" style="color: #6c757d; margin-right: 6px;"></i>
                                                                                                    {{ __('dashboard.Additional Charges') }}
                                                                                                </h6>
                                                                                                <div class="supplement-item mb-2" style="display: flex; justify-content: center; align-items: center; padding: 10px 14px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                                                                                                    <span style="font-size: 12px; color: #6c757d; font-style: italic;">
                                                                                                        {{ __('dashboard.No additional charges') }}
                                                                                                    </span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                    <div class="refund-policy-section">
                                                                                        <h6 class="refund-policy-title" style="font-size: 14px; font-weight: bold; color: #333; margin-bottom: 10px;">
                                                                                            <i class="fas fa-undo-alt" style=" margin-right: 6px;"></i>
                                                                                            {{ __('dashboard.Refund Policy') }}
                                                                                        </h6>
                                                                                        @if (@$room['IsRefundable'])
                                                                                            <div class="refund-badge refund-positive" style="display: inline-block; padding: 10px 14px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; font-size: 12px; font-weight: 500; box-shadow: 0 2px 4px rgba(40, 167, 69, 0.15);">
                                                                                                <i class="fas fa-check-circle me-1" style="color: #28a745;"></i>
                                                                                                {{ __('dashboard.Fully Refundable') }}
                                                                                            </div>
                                                                                        @else
                                                                                            <div class="refund-badge refund-negative" style="display: inline-block; padding: 10px 14px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; font-size: 12px; font-weight: 500; box-shadow: 0 2px 4px rgba(220, 53, 69, 0.15);">
                                                                                                <i class="fas fa-times-circle me-1" style="color: #dc3545;"></i>
                                                                                                {{ __('dashboard.Non-Refundable') }}
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    
                                                                                    {{-- Cancellation Policy Section --}}
                                                                                    @if (@$room['CancelPolicies'] && count(@$room['CancelPolicies']) > 0)
                                                                                        <div class="cancellation-policy-section mt-3 w-100">
                                                                                            <h6 class="cancellation-policy-title" style="font-size: 14px; font-weight: bold; color: #333; margin-bottom: 10px;">
                                                                                                <i class="fas fa-calendar-times" style="color: #dc3545; margin-right: 6px;"></i>
                                                                                                {{ __('dashboard.Cancellation Policy') }}
                                                                                            </h6>
                                                                                            @foreach (@$room['CancelPolicies'] as $policy)
                                                                                                <div class="cancellation-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; margin-bottom: 8px; background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%); border-radius: 8px; border: 1px solid #fc8181; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                                                                    <div class="cancellation-info">
                                                                                                        <div class="cancellation-date" style="font-size: 12px; color: #742a2a; font-weight: 600;">
                                                                                                            <i class="fas fa-clock" style="color: #e53e3e; font-size: 10px; margin: 0px 6px;"></i>
                                                                                                            {{ __('dashboard.From') }}: {{ \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', @$policy['FromDate'])->format('M d, Y') }}
                                                                                                        </div>
                                                                                                        <div class="cancellation-charge-type" style="font-size: 11px; color: #9f2c32; margin-top: 2px;">
                                                                                                            @if(@$policy['ChargeType'] == 'Percentage')
                                                                                                                {{ __('dashboard.Percentage') }}
                                                                                                            @elseif(@$policy['ChargeType'] == 'Amount')
                                                                                                                {{ __('dashboard.amount') }}
                                                                                                            @elseif(@$policy['ChargeType'] == 'Fixed')
                                                                                                                {{ __('dashboard.Fixed') }}
                                                                                                            @else
                                                                                                                {{ ucfirst(@$policy['ChargeType']) }}
                                                                                                            @endif
                                                                                                            {{ __('dashboard.charge') }}
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="cancellation-charge">
                                                                                                        <span class="charge-amount" style="font-size: 13px; font-weight: bold; color: #c53030;">
                                                                                                            @if(@$policy['ChargeType'] == 'Percentage')
                                                                                                                {{ @$policy['CancellationCharge'] }}%
                                                                                                            @else
                                                                                                                {{ @$policy['CancellationCharge'] }} {{ __('dashboard.SAR') }}
                                                                                                            @endif
                                                                                                        </span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @else
                                                                                        {{-- Placeholder for consistent spacing --}}
                                                                                        <div class="cancellation-policy-placeholder mt-3">
                                                                                            <h6 class="cancellation-policy-title" style="font-size: 14px; font-weight: bold; color: #666; margin-bottom: 10px;">
                                                                                                <i class="fas fa-calendar-times" style="color: #6c757d; margin-right: 6px;"></i>
                                                                                                {{ __('dashboard.Cancellation Policy') }}
                                                                                            </h6>
                                                                                            <div class="cancellation-item" style="display: flex; justify-content: center; align-items: center; padding: 10px 14px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                                                                                                <span style="font-size: 12px; color: #6c757d; font-style: italic;">
                                                                                                    {{ __('dashboard.No cancellation policy specified') }}
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                    <hr class="my-2">
                                                                                </div>
                                                                                <div class="section-second">
                                                                                    <span class="hotel-price">
                                                                                        <span class="new_price">
                                                                                            {{ $room['TotalFare'] }}
                                                                                            {{ __('dashboard.SAR') }}
                                                                                        </span>
                                                                                    </span>
                                                                                    <span class="hotel-fees">
                                                                                        {{ __('dashboard.Included taxes & fees') }}
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            <button
                                                                                class="btn btn-warning py-2 mt-3 mb-2 px-5 w-100 animated fadeIn fw-bold select-room-btn"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#refundModal"
                                                                                data-booking-code="{{ @$room['BookingCode'] }}"
                                                                                data-hotel-code="{{ $hotel->code }}"
                                                                                data-refundable="{{ @$room['IsRefundable'] ? 'true' : 'false' }}"
                                                                                data-total-fare="{{ $room['TotalFare'] }}">
                                                                                {{ __('dashboard.select_this') }}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="col-md-12">
                                                                    <div class="alert alert-warning text-center">
                                                                        {{ __('dashboard.no_rooms_available') }}
                                                                    </div>
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=maps,marker&v=beta"
        defer></script>
@endsection
@section('newCss')
    <style>
        .fixed-top {
            position: fixed;
            top: 0;
            z-index: 1050;
            background-color: white;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .fixed-top a {
            padding-top: 1em !important;
            padding-bottom: 1em !important;
        }

        .hotel-tabs+.tab-content {
            padding-top: 60px;
        }

        /* Room card consistency fixes */
        .rooms-cards .card {
            /* min-height: 600px; */
            display: flex;
            flex-direction: column;
        }

        .rooms-cards .card .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* .rooms-cards .card .collection-sections {
            margin-top: auto;
        } */

        /* Ensure consistent spacing for room sections */
        .rooms-cards .meal-type-container,
        .rooms-cards .inclusions-container,
        .rooms-cards .supplements-section {
            margin-bottom: 15px;
        }

        /* Add consistent height for empty states */
        .room-content-sections {
            min-height: 120px;
            display: flex;
            flex-direction: column;
        }

        /* Meal type placeholder for consistency */
        .meal-type-placeholder {
            margin: 10px 0;
        }

        /* Inclusion placeholder for consistency */
        .inclusion-placeholder {
            height: 60px;
            margin: 10px 0;
        }

        /* Promotion placeholder for consistency */
        .promotion-placeholder {
            height: 40px;
            margin: 10px 0;
        }

        /* Supplements placeholder for consistency */
        .supplements-placeholder {
            height: 80px;
            margin: 10px 0;
        }

        /* Pulse animation for meal type highlight */
        @keyframes pulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.1);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Meal type card hover effects */
        .meal-type-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 152, 0, 0.15) !important;
            transition: all 0.3s ease;
        }

        .meal-type-badge:hover {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }

        /* Refundable placeholder for consistency */
        .refundable-placeholder {
            height: 30px;
            margin: 10px 0;
        }
    </style>
@endsection
@section('newJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const myTab = document.getElementById('myTab');
            const tabLinks = myTab.querySelectorAll('.nav-link');
            const tabContent = document.querySelector('.hotel-content-tabs');
            const tabPanes = tabContent.querySelectorAll('.tab-pane');

            // Store the original tab position
            const tabTop = myTab.offsetTop;

            // Scroll event listener
            window.addEventListener('scroll', function() {
                // Check if scroll position is past the tab container
                if (window.scrollY >= tabTop) {
                    myTab.classList.add('fixed-top');
                } else {
                    myTab.classList.remove('fixed-top');
                }

                // Update active tab based on scroll position
                updateActiveTab();
            });

            // Function to update the active tab based on visible section
            const updateActiveTab = () => {
                let currentTab = null;

                tabPanes.forEach((pane) => {
                    const rect = pane.getBoundingClientRect();
                    // If the top of the pane is within the viewport (adjusted by 60px)
                    if (rect.top <= 60 && rect.bottom >= 60) {
                        currentTab = pane.id;
                    }
                });

                // Update the active tab link
                if (currentTab) {
                    tabLinks.forEach((link) => {
                        const target = link.getAttribute('href').replace('#', '');
                        if (target === currentTab) {
                            link.classList.add('active');
                        } else {
                            link.classList.remove('active');
                        }
                    });
                }
            };

            // Click event for smooth scrolling to the selected tab pane
            tabLinks.forEach((link) => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').replace('#', '');
                    const targetPane = document.getElementById(targetId);
                    const offset = targetPane.offsetTop - myTab
                        .offsetHeight; // Adjust for the fixed tab height
                    window.scrollTo({
                        top: offset,
                        behavior: 'smooth'
                    });
                });
            });

            const selectRoomButtons = document.querySelectorAll('.select-room-btn');
            const refundModal = document.getElementById('refundModal');
            const refundMessage = document.getElementById('refundMessage');
            const confirmRoomSelection = document.getElementById('confirmRoomSelection');

            selectRoomButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const isRefundable = this.getAttribute('data-refundable') === 'true';
                    const totalFare = this.getAttribute('data-total-fare');
                    const bookingCode = this.getAttribute('data-booking-code');
                    const hotelCode = this.getAttribute('data-hotel-code');
                    if (isRefundable) {
                        refundMessage.innerHTML = `{{ __('dashboard.This room is fully refundable. Refundable amount') }}: ${totalFare} {{ __('dashboard.sar') }}.`;
                    } else {
                        refundMessage.innerHTML = `{{ __('dashboard.This room is non-refundable. Total amount') }}: ${totalFare} {{ __('dashboard.sar') }}.`;
                    }

                    confirmRoomSelection.setAttribute('href', `/select-room/${bookingCode}?hotel=${hotelCode}`);
                });
            });
        });
    </script>
@endsection
