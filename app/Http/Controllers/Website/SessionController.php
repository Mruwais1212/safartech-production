<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SessionController
{
    public function showSessionData(Request $request)
    {
        $allSessionData = Session::all();
        return response()->json($allSessionData);
    }
}
