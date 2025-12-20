<?php

namespace App\Http\Controllers\Website;

use App\Http\Requests\Website\ContactUsRequest;
use App\Models\Panel\Contact;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        return view('website.contact-us');
    }

    public function store(ContactUsRequest $request)
    {
        Contact::create($request->validated());

        return redirect()->back()->with('success', 'Your message has been sent successfully');
    }
}
