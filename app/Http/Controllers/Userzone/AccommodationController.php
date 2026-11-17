<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;

use App\Models\Accommodation;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function show(Accommodation $accommodation): View
    {
        return view('userzone.accommodations.show', compact('accommodation'));
    }
}
