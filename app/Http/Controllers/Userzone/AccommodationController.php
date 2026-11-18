<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Selection;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function show(Selection $selection, Accommodation $accommodation): View
    {
        return view('userzone.accommodations.show', compact('accommodation'));
    }
}
