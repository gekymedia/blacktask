<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
  // app/Http/Controllers/CalendarController.php
public function index()
{
    return view('calendar');
}
}
