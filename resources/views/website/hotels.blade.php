@extends('website.layout')
@section('title', __('dashboard.home'))
@section('content')
    <div style="background-image: url('/site/img/hotels.jpg');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div>

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.Hotels') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $hotels = json_decode($hotels);
        $hotels = (new Illuminate\Pagination\LengthAwarePaginator(
            items: is_array($hotels) ? $hotels : $hotels->data,
            total: is_array($hotels) ? count($hotels) : $hotels->total,
            perPage: is_array($hotels) ? 1 : $hotels->per_page,
            currentPage: is_array($hotels) ? 1 : $hotels->current_page,
            options: [],
        ))->withPath('/hotels');
    @endphp

    <div class="container search-page hotel-page position-relative">
        <div class="row">
            <div class="col-md-12">
                <form class="steps-form" method="get" action="/hotels">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-3 pe-4 filter-sidebar">
                                <div class="remove-class">
                                    <a href="#" class="remove-class-link">
                                        <i class="fa fa-times text-warning fa-2x"></i>
                                    </a>
                                    <a href="#" id="resetbtn" class="clear-class-link">
                                        Clear
                                    </a>
                                </div>
                                <br>
                                <div class="search-sidebar">
                                    <div class="filter-card meals-plans">
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle">{{ __('dashboard.hotel_name') }}</h3>
                                        </div>
                                        <div class="grid">
                                            <label class="card">
                                                <input name="hotel_name" type="text" id="hotel_name" class="form-control"
                                                    value="{{ request()->hotel_name }}"
                                                    placeholder="{{ __('dashboard.hotel_name') }}">
                                            </label>
                                        </div>
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle">Price</h3>
                                        </div>
                                        <div class="col-md-12 my-4">
                                            <div class="form-group">
                                                <input
                                                    type="text"
                                                    class="range-example-input"
                                                    name="price_range"
                                                    value="{{@$preferences['min_budget'].','.@$preferences['max_budget']}}"
                                                    data-min="0"
                                                    data-max="500"
                                                    data-step="10"
                                                    currency="SAR"
                                                >
                                            </div>
                                        </div>

                                        <br>
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle">{{ __('dashboard.hotel_rating') }}</h3>
                                        </div>
                                        <div class="grid">
                                            <label class="card">
                                                <input name="hotel_rating" class="radio" type="checkbox"
                                                    {{ request()->hotel_rating == 'OneStar' ? 'checked' : '' }}
                                                    value="OneStar">
                                                <span class="plan-details">
                                                    <div class="plan-title text-warning">
                                                        1 <i class="fas fa-star text-warning"></i>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="hotel_rating" class="radio" type="checkbox"
                                                    {{ request()->hotel_rating == 'TwoStar' ? 'checked' : '' }}
                                                    value="TwoStar">
                                                <span class="plan-details">
                                                    <div class="plan-title text-warning">
                                                        2 <i class="fas fa-star text-warning"></i>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="hotel_rating" class="radio" type="checkbox"
                                                    {{ request()->hotel_rating == 'ThreeStar' ? 'checked' : '' }}
                                                    value="ThreeStar">
                                                <span class="plan-details">
                                                    <div class="plan-title text-warning">
                                                        3 <i class="fas fa-star text-warning"></i>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="hotel_rating" class="radio" type="checkbox"
                                                    {{ request()->hotel_rating == 'FourStar' ? 'checked' : '' }}
                                                    value="FourStar">
                                                <span class="plan-details">
                                                    <div class="plan-title text-warning">
                                                        4 <i class="fas fa-star text-warning"></i>
                                                    </div>
                                                </span>
                                            </label>
                                            <label class="card">
                                                <input name="hotel_rating" class="radio" type="checkbox"
                                                    {{ request()->hotel_rating == 'FiveStar' ? 'checked' : '' }}
                                                    value="FiveStar">
                                                <span class="plan-details">
                                                    <div class="plan-title text-warning">
                                                        5 <i class="fas fa-star text-warning"></i>
                                                    </div>
                                                </span>
                                            </label>
                                        </div>
                                        {{-- <br>
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle">{{ __('dashboard.meal_type') }}</h3>
                                        </div>
                                        <div class="grid">
                                            <ul class="filter-list">
                                                <li>
                                                    <label class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="All">
                                                        <span class="form-check-label">All</span>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Full_Board">
                                                        <span class="form-check-label">Full Board</span>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Half_Board">
                                                        <span class="form-check-label">Half Board</span>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="Breakfast">
                                                        <span class="form-check-label">Breakfast</span>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="Room_Only">
                                                        <span class="form-check-label">Room Only</span>
                                                    </label>
                                                 </li>
                                            </ul>
                                        </div> --}}
                                        <br>
                                        <div class="filter-card-title">
                                            <h3 class="filter-subtitle">{{ __('dashboard.can_refund') }}</h3>
                                        </div>
                                        <div class="grid">
                                            <label class="card">
                                                <input name="refundable" type="radio" class="form-check-input"
                                                    value="true" {{ request()->refundable == 'true' ? 'checked' : '' }}>
                                                <span class="form-check-label">{{ __('dashboard.refundable') }}</span>
                                            </label>
                                            <label class="card">
                                                <input name="refundable" type="radio" class="form-check-input"
                                                    value="false" {{ request()->refundable == 'false' ? 'checked' : '' }}>
                                                <span class="form-check-label">{{ __('dashboard.not_refundable') }}</span>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="btn btn-outline-warning py-3 w-100">{{ __('dashboard.Search') }}</button>
                                </div>
                            </div>

                            <div class="col-md-9 big-section ps-4">
                                <button type="button"
                                    class="btn btn-warning py-2 w-100 filter-btn">{{ __('dashboard.Sort & Filter') }}</button>
                                <div class="search-top-bar">
                                    <div class="flight-box flight-from">
                                        <i class="bi bi-geo-alt"></i>
                                        <div class="location-info">
                                            <p>{{ __('dashboard.Arrival Location') }}</p>
                                            <p>{{ isset(session('preferences')['destination_name']) ? session('preferences')['destination_name'] : '' }}
                                                ({{ request()->destination }})</p>
                                        </div>
                                    </div>
                                    <div class="flight-box date-from">
                                        <i class="bi bi-calendar4"></i>
                                        <div class="date-info">
                                            <p>{{ __('dashboard.Check IN Date') }}</p>
                                            <p>{{ request()->checkin }}</p>
                                        </div>
                                    </div>
                                    <div class="flight-box date-from">
                                        <i class="bi bi-calendar4"></i>
                                        <div class="date-info">
                                            <p>{{ __('dashboard.Check Out Date') }}</p>
                                            <p>{{ request()->checkout }}</p>
                                        </div>
                                    </div>
                                    <div class="flight-box date-from">
                                        <i class="bi bi-user"></i>
                                        <div class="date-info">
                                            <p>{{ __('dashboard.Travelers') }}</p>
                                            @php
                                                $totalAdults = 0;
                                                $totalChildren = 0;
                                                $totalInfants = 0;
                                                
                                                // Calculate totals from paxRooms if available
                                                if (isset(session('preferences')['paxRooms']) && is_array(session('preferences')['paxRooms'])) {
                                                    foreach (session('preferences')['paxRooms'] as $room) {
                                                        $totalAdults += $room->Adults ?? 0;
                                                        $totalChildren += $room->Children ?? 0;
                                                        // Count children with age 1 as infants (babies)
                                                        if (isset($room->ChildrenAges) && is_array($room->ChildrenAges)) {
                                                            $infantsInRoom = count(array_filter($room->ChildrenAges, function($age) {
                                                                return $age == 1;
                                                            }));
                                                            $totalInfants += $infantsInRoom;
                                                            $totalChildren -= $infantsInRoom; // Remove infants from children count
                                                        }
                                                    }
                                                } else {
                                                    // Fallback to old structure
                                                    $totalAdults = request()->adults ?? 0;
                                                    $totalChildren = request()->children ?? 0;
                                                    $totalInfants = request()->infants ?? 0;
                                                }
                                                
                                                $totalTravelers = $totalAdults + $totalChildren + $totalInfants;
                                            @endphp
                                            <p>{{ $totalTravelers }}
                                                {{ __('dashboard.Travelers') }}, {{ request()->rooms }}
                                                {{ __('dashboard.Rooms') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="sort-section">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group mt-0">
                                                <label>{{ __('dashboard.Sort By') }}: </label>
                                                <select class="form-control" id="sort" name="sort">
                                                    <option value="price_asc"
                                                        {{ request()->sort == 'price_asc' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Price Low to High') }}</option>
                                                    <option value="price_desc"
                                                        {{ request()->sort == 'price_desc' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Price High to Low') }}
                                                    </option>
                                                    <option value="distance_asc"
                                                        {{ request()->sort == 'distance_asc' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Distance Low to High') }}</option>
                                                    <option value="distance_desc"
                                                        {{ request()->sort == 'distance_desc' ? 'selected' : '' }}>
                                                        {{ __('dashboard.Distance High to Low') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4"></div>
                                <div class="hotels-cards">
                                    @foreach ($hotels as $hotel)
                                        <div class="card">
                                            <div class="owl-carousel hotel-image-carousel">
                                                @if(count(collect(@$hotel->images)->take(5)->toArray()) > 0)
                                                @foreach (collect(@$hotel->images)->take(5)->toArray() as $image)
                                                    <div class="owl-carousel-item">
                                                        <div class="img-container">
                                                            <img src="{{ $image }}" />
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @else
                                                <div class="owl-carousel-item">
                                                    <div class="img-container">
                                                        <img src="/no-image.png" />
                                                    </div>
                                                </div>
                                                @endif
                                            </div>

                                            <div data-href="/hotel/{{ @$hotel->code }}" class="card-content">
                                                <a href="/hotel/{{ @$hotel->code }}" class="hotel-name">
                                                    <h2>{{ app()->getLocale() == 'ar' ? ($hotel->name_ar ?: $hotel->name_en) : ($hotel->name_en ?: $hotel->name_ar) }}
                                                    </h2>
                                                </a>
                                                <ul class="hotel-list-options text-start justify-content-start">
                                                    @foreach (collect(app()->getLocale() == 'ar' ? ($hotel->facilities_ar ?: $hotel->facilities_en) : ($hotel->facilities_en ?: $hotel->facilities_ar))->take(4)->toArray() as $facility)
                                                        @php
                                                            $maxLength = 50; // Set maximum character length
                                                            $truncatedText = Str::length($facility) > $maxLength ? Str::limit($facility, $maxLength, '...') : $facility;
                                                        @endphp
                                                        <li
                                                            style="font-size: 12px;background: aquamarine;border-radius: 15px;box-shadow: aquamarine 2px 2px; cursor: pointer;"
                                                            title="{{ $facility }}"
                                                            data-toggle="tooltip"
                                                            data-placement="top">
                                                            {{ $truncatedText }}</li>
                                                    @endforeach
                                                </ul>

                                                <?php
                                                $text = Str::limit(app()->getLocale() == 'ar' ? ($hotel->description_ar ?: $hotel->description_en) : ($hotel->description_en ?: $hotel->description_ar), 200, '...');
                                                $direction = preg_match('/\p{Arabic}/u', $text) ? 'rtl' : 'ltr';
                                                ?>
                                                <hr>
                                                <div style="direction:{{ $direction }}" class="excerpt text-start">
                                                    {!! $text !!}
                                                </div>
                                                <hr>
                                                <div class="collection-sections">
                                                    <div class="section-first">
                                                        <span class="text-success offer-refund">
                                                            {{ @$hotel->rooms->is_refundable ? __('dashboard.refundable') : __('dashboard.not_refundable') }}
                                                        </span>
                                                        <div class="hotel-rate">
                                                            <div class="hotel-stars">
                                                                <input class="rating rating-loading" data-min="0"
                                                                    data-max="5" data-step="1"
                                                                    value="{{ @$hotel->new_rating }}" data-size="xs"
                                                                    disabled="" hidden>
                                                            </div>
                                                            {{-- <span class="rate-box">
                                                            {{ @$hotel->rating }}</span> --}}
                                                            <span class="hotel-price">{{ @$hotel->rooms->Price }}
                                                                {{ __('dashboard.SAR') }}</span>
                                                        </div>
                                                        <div class="hotel-location">
                                                            <span
                                                                class="location-title">{{ __('dashboard.distance_from_center_of_city') }}
                                                                : {{ round(@$hotel->distance, 2) }}
                                                                {{ __('dashboard.meter') }} </span>
                                                            <span class="location-title">{{ __('dashboard.address') }} :
                                                            </span>
                                                            <span class="location-desc">{{ @$hotel->address }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="section-second">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    {{ $hotels->appends(request()->all())->links() }}

                                    @if ($hotels->isEmpty())
                                        <div class="card text-center d-block"
                                            style="width: 100%;height: 300px;padding-top: 135px;">
                                            {{ __('dashboard.No hotels found') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            $('input[name="hotel_rating"]').on('click', function() {
                // remove all checked
                $('input[name="hotel_rating"]').each(function() {
                    $(this).prop('checked', false);
                });

                // add checked
                $(this).prop('checked', true);
            });

            $('#sort').on('change', function() {
                var sort = $(this).val();
                var url = new URL(window.location.href);
                var hotel_name = $('#hotel_name').val();
                var hotel_rating = $('input[name="hotel_rating"]:checked').val();
                var refundable = $('input[name="refundable"]:checked').val();
                url.searchParams.set('sort', sort);
                url.searchParams.set('hotel_name', hotel_name);
                url.searchParams.set('hotel_rating', hotel_rating ?? '');
                url.searchParams.set('refundable', refundable ?? '');
                window.location.href = url;
            });
        });
    </script>
@endsection
@section('newCss')
    <style>
        .card-content {
            cursor: pointer;
        }

        .hotels-cards .card .hotel-image-carousel {
            /* flex: 1; */
            width: 33.33%;
            height: auto;
        }

        .hotels-cards .card {
            width: 100%;
            display: flex;
            flex-direction: row;
            align-items: stretch;
        }
        .hotels-cards .card .img-container{
            width: 100%;
            /* height: 200px;
            overflow: hidden; */
        }
        
        /* Custom tooltip styling */
        .tooltip {
            z-index: 9999;
        }
        
        .tooltip-inner {
            background-color: #333;
            color: white;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            max-width: 250px;
            text-align: center;
            word-wrap: break-word;
        }
        
        .hotel-list-options li[data-toggle="tooltip"]:hover {
            background: #20b2aa !important;
            transform: scale(1.05);
            transition: all 0.2s ease;
        }
    </style>
@endsection
@section('newJs')
    <script>
        $(document).ready(function() {
            $('.card-content').on('click', function() {
                const url = $(this).data('href');
                window.location.href = url;
            });
        });
    </script>
@endsection
