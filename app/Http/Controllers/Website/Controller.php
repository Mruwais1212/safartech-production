<?php

namespace App\Http\Controllers\Website;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

abstract class Controller
{
    public function __construct(Request $request)
    {
        $validLocales = ['ar', 'en'];

        if ($request->currency) {
            Session::put('currency', $request->currency);
        }

        if ($request->lang) {
            $lang = strtok($request->lang, '?');
            if (in_array($lang, $validLocales)) {
                Session::put('lang', $lang);
            } else {
                Session::put('lang', 'ar');
            }
        }

        $locale = Session::get('lang', 'ar');
        app()->setLocale($locale);
        Carbon::setLocale($locale);
    }
}