<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Enums\UserType;
use App\Http\Controllers\Panel\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Services\TBOFlightBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ixudra\Curl\Facades\Curl;

class HomeController extends Controller
{
    public function index()
    {
        $statistic = [
            'count_users' => User::where('user_type_id', UserType::CLIENT), //->where('activate', 1)
            'count_supervisors' => User::where('user_type_id', UserType::SUPERVISOR), //->where('activate', 1),
            'count_reservations' => Reservation::where('payment_method', '>=', 1),
            'count_reservations_today' => Reservation::where('payment_method', '>=', 1)->whereDate('created_at', today()),
            'count_reservations_this_month' => Reservation::where('payment_method', '>=', 1)->whereMonth('created_at', now()->month),
            'count_reservations_this_year' => Reservation::where('payment_method', '>=', 1)->whereYear('created_at', now()->year),
            'count_hotel_reservations' => Reservation::whereHas('hotel')->where('payment_method', '>=', 1),
            'count_flight_reservations' => Reservation::whereHas('flight')->where('payment_method', '>=', 1),
        ];

        $icons = [
            'count_users' => 'fa fa-users',
            'count_supervisors' => 'fa fa-user',
            'count_reservations' => 'fa fa-calendar',
            'count_reservations_today' => 'fa fa-calendar',
            'count_reservations_this_month' => 'fa fa-calendar',
            'count_reservations_this_year' => 'fa fa-calendar',
            'count_hotel_reservations' => 'fa fa-hotel',
            'count_flight_reservations' => 'fa fa-plane',
        ];

        $links = [
            'count_users' => '/admin-panel/users',
            'count_supervisors' => '/admin-panel/supervisor',
            'count_reservations' => '/admin-panel/reservations',
            'count_reservations_today' => '/admin-panel/reservations?date=today',
            'count_reservations_this_month' => '/admin-panel/reservations?date=month',
            'count_reservations_this_year' => '/admin-panel/reservations?date=year',
            'count_hotel_reservations' => '/admin-panel/hotel-reservations',
            'count_flight_reservations' => '/admin-panel/flight-reservations',
        ];

        $statistic_in_one_query = User::statisticInOneQuery($statistic);
        $statistic_in_month = Reservation::where('payment_method', '>=', 1)->StatisticInMonth();
        $statistic_in_day = Reservation::where('payment_method', '>=', 1)->StatisticInDay();
        $statistic_in_term = Reservation::where('payment_method', '>=', 1)->StatisticInTerm();
        $statistic_in_hours = Reservation::where('payment_method', '>=', 1)->StatisticInHour();
        $hotel_reservations_in_month = Reservation::whereHas('hotel')->where('payment_method', '>=', 1)->StatisticInMonth();
        $flight_reservations_in_month = Reservation::whereHas('flight')->where('payment_method', '>=', 1)->StatisticInMonth();

        return view('admin.index', compact(
            'statistic_in_one_query',
            'icons',
            'statistic_in_day',
            'statistic_in_term',
            'statistic_in_month',
            'statistic_in_hours',
            'hotel_reservations_in_month',
            'flight_reservations_in_month',
            'links'
        ));
    }

    public function editProfile()
    {
        return view('admin.edit-profile');
    }

    public function updateProfile(Request $request)
    {
        Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,id,'.auth('admin')->id(),
                'phone' => 'required|string|max:255',
                'password' => 'nullable|string|min:6|confirmed',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]
        );

        $user = User::find(auth('admin')->id());

        $photoName = $user->photo; // Keep existing photo by default
        
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time().'.'.$photo->getClientOriginalExtension();
            
            // Ensure the uploads directory exists
            $uploadsPath = public_path('uploads');
            if (!file_exists($uploadsPath)) {
                mkdir($uploadsPath, 0755, true);
            }
            
            $photo->move($uploadsPath, $photoName);
        }

        $user->update(
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => ltrim($request->phone, 0),
                'password' => $request->password ? bcrypt($request->password) : $user->password,
                'photo' => $photoName,
            ]
        );

        return redirect()->back()->with('success', __('messages.profile_updated'));
    }

    public function getTboFlightBalance(){
        $username = config('services.tbo_flight.username');
        $password = config('services.tbo_flight.password');

        $response = Curl::to('https://xmloutapi.tboair.com/api/v1/Authenticate/ValidateAgency/')
            ->withData([
                'UserName' => $username,
                'Password' => $password,
                'BookingMode' => 'API',
                'IPAddress' => request()->ip(),
            ] )
            ->asJson()
            ->get();
dd($response);
        $response= (new TBOFlightBookingService)->getAgencyBalance();
        dd($response);
    }
}
