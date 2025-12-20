<?php

namespace App\Http\Controllers\Website;

use App\Models\Destination;
use App\Models\PageContent;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Mail\BookingEmail;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [new Middleware(['auth:web', 'web'], except: ['index', 'show'])];
    }

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        // Mail::raw('Test email', function ($message) {
        //     $message->to('doba.nabil@gmail.com')->subject('Test Email');
        // });
        // $reservation = Reservation::first();
        // Mail::to('dobanabil40@gmail.com')->send(new BookingEmail($reservation));
        // return 'success';
        $trips = Destination::inRandomOrder()->take(8)->get();
        $page_contents = PageContent::where('url', 'home')->get();

        return view('website.home', compact('trips', 'page_contents'));
    }

    public function savedTrips()
    {
        // $trips = Destination::inRandomOrder()->take(8)->get();

        // return view('website.saved-trips', compact('trips'));
        $reservations = Reservation::with('hotel', 'flight')->where('user_id', auth('web')->id())->where('payment_method', '!=', 0)->get();

        return view('website.my-trips', compact('reservations'));
    }

    public function passengerInformation()
    {
        return view('website.passenger-information');
    }

    public function tripDetails($id)
    {
        $trip = Destination::find($id);

        if (! $trip) {
            return abort(404);
        }

        return view('website.trip-details', compact('trip'));
    }
}
