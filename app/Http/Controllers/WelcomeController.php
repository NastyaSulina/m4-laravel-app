<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $latestSelection = $request->user()?->selections()->where('status', 'active')->latest()->orderByDesc('id')->first();

        return view('welcome', compact('latestSelection'));
    }
}
