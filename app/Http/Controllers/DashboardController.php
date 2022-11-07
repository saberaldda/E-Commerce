<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('layouts.admin');
    }
}
// Regular Expreion
// src="([^"]+)"
// src="{{ asset('assets/front/$1') }}"
