<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function show(Accommodation $accommodation): View
    {
        return view('accommodations.show', compact('accommodation'));
    }
}
