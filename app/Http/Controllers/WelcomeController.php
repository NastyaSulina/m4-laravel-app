<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): string
    {
        $user = $request->user();
        $key = $user ? "welcome:user:{$user->id}" : 'welcome:guest';

        return cache()->flexible($key, [30, 60], function () use ($user): string {
            $latestSelection = $user?->selections()
                ->where('status', 'active')
                ->latest()
                ->orderByDesc('id')
                ->first();

            return view('welcome', compact('latestSelection'))->render();
        });
    }
}
