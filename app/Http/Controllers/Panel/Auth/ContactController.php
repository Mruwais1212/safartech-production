<?php

namespace App\Http\Controllers\Panel\Auth;

use App\Http\Controllers\Panel\Controller;
use App\Models\Panel\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::all();

        return view('admin.panel.contact.all', compact('contacts'));
    }
}
