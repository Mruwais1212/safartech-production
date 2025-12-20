<?php

namespace App\Http\Controllers\Website;

use App\Models\Tour;
use Illuminate\Http\Request;

class MyLatestAiTripController extends Controller
{
    public function index()
    {
        $reservations = Tour::where('user_id', auth('web')->id())->orderBy('id', 'desc')->get();
        return view('website.my-ai-trips', compact('reservations'));
    }

    public function show($id)
    {
        $reservation = Tour::where('user_id', auth('web')->id())->findOrFail($id);
        return view('website.my-ai-trip', compact('reservation'));
    }

    public function data($id)
    {
         $tour = Tour::where('user_id', auth('web')->id())->findOrFail($id);
         return json_decode($tour['tour_data'], true);
    }
}
