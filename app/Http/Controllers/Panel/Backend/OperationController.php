<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Models\Reservation;
use App\Models\User;

class OperationController extends Controller
{
    public function index()
    {
        $statistic = [
            'count_reservations' => Reservation::where('payment_method', '>=', 1),
            'count_hotel_reservations' => Reservation::whereHas('hotel')->where('payment_method', '>=', 1),
            'count_flight_reservations' => Reservation::whereHas('flight')->where('payment_method', '>=', 1),
            'count_hotel_and_flight_reservations' => Reservation::whereHas('hotel')->whereHas('flight')->where('payment_method', '>=', 1),

            'count_reservations_today' => Reservation::where('payment_method', '>=', 1)->whereDate('created_at', today()),
            'count_hotel_reservations_today' => Reservation::whereHas('hotel')->where('payment_method', '>=', 1)->whereDate('created_at', today()),
            'count_flight_reservations_today' => Reservation::whereHas('flight')->where('payment_method', '>=', 1)->whereDate('created_at', today()),
            'count_hotel_and_flight_reservations_today' => Reservation::whereHas('hotel')->whereHas('flight')->where('payment_method', '>=', 1)->whereDate('created_at', today()),

            'count_reservations_this_month' => Reservation::where('payment_method', '>=', 1)->whereMonth('created_at', now()->month),
            'count_hotel_reservations_this_month' => Reservation::whereHas('hotel')->where('payment_method', '>=', 1)->whereMonth('created_at', now()->month),
            'count_flight_reservations_this_month' => Reservation::whereHas('flight')->where('payment_method', '>=', 1)->whereMonth('created_at', now()->month),
            'count_hotel_and_flight_reservations_this_month' => Reservation::whereHas('hotel')->whereHas('flight')->where('payment_method', '>=', 1)->whereMonth('created_at', now()->month),

            'count_reservations_this_year' => Reservation::where('payment_method', '>=', 1)->whereYear('created_at', now()->year),
            'count_hotel_reservations_this_year' => Reservation::whereHas('hotel')->where('payment_method', '>=', 1)->whereYear('created_at', now()->year),
            'count_flight_reservations_this_year' => Reservation::whereHas('flight')->where('payment_method', '>=', 1)->whereYear('created_at', now()->year),
            'count_hotel_and_flight_reservations_this_year' => Reservation::whereHas('hotel')->whereHas('flight')->where('payment_method', '>=', 1)->whereYear('created_at', now()->year),
        ];

        $icons = [
            'count_reservations' => 'fa fa-calendar',
            'count_hotel_reservations' => 'fa fa-hotel',
            'count_flight_reservations' => 'fa fa-plane',
            'count_hotel_and_flight_reservations' => 'fa fa-map-marker',

            'count_reservations_today' => 'fa fa-calendar',
            'count_hotel_reservations_today' => 'fa fa-hotel',
            'count_flight_reservations_today' => 'fa fa-plane',
            'count_hotel_and_flight_reservations_today' => 'fa fa-map-marker',

            'count_reservations_this_month' => 'fa fa-calendar',
            'count_hotel_reservations_this_month' => 'fa fa-hotel',
            'count_flight_reservations_this_month' => 'fa fa-plane',
            'count_hotel_and_flight_reservations_this_month' => 'fa fa-map-marker',

            'count_reservations_this_year' => 'fa fa-calendar',
            'count_hotel_reservations_this_year' => 'fa fa-hotel',
            'count_flight_reservations_this_year' => 'fa fa-plane',
            'count_hotel_and_flight_reservations_this_year' => 'fa fa-map-marker',
        ];

        $links = [
            'count_reservations' => '/admin-panel/reservations',
            'count_hotel_reservations' => '/admin-panel/hotel-reservations',
            'count_flight_reservations' => '/admin-panel/flight-reservations',
            'count_hotel_and_flight_reservations' => '/admin-panel/hotel-and-flight-reservations',

            'count_reservations_today' => '/admin-panel/reservations?date=today',
            'count_hotel_reservations_today' => '/admin-panel/hotel-reservations?date=today',
            'count_flight_reservations_today' => '/admin-panel/flight-reservations?date=today',
            'count_hotel_and_flight_reservations_today' => '/admin-panel/hotel-and-flight-reservations?date=today',

            'count_reservations_this_month' => '/admin-panel/reservations?date=month',
            'count_hotel_reservations_this_month' => '/admin-panel/hotel-reservations?date=month',
            'count_flight_reservations_this_month' => '/admin-panel/flight-reservations?date=month',
            'count_hotel_and_flight_reservations_this_month' => '/admin-panel/hotel-and-flight-reservations?date=month',

            'count_reservations_this_year' => '/admin-panel/reservations?date=year',
            'count_hotel_reservations_this_year' => '/admin-panel/hotel-reservations?date=year',
            'count_flight_reservations_this_year' => '/admin-panel/flight-reservations?date=year',
            'count_hotel_and_flight_reservations_this_year' => '/admin-panel/hotel-and-flight-reservations?date=year',
        ];

        $statistic_in_one_query = User::statisticInOneQuery($statistic);

        return view('admin.backend.operation.index', compact(
            'statistic_in_one_query',
            'icons',
            'links',
        ));
    }
}
