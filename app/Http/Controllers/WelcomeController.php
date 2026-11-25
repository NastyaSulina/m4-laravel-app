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
        $user = $request->user();

        $data = $user
            ? cache()->flexible("welcome:latest-selection:{$user->id}", [30, 60], function () use ($user): array {
                return [
                    'latestSelection' => $user->selections()
                        ->where('status', 'active')
                        ->latest()
                        ->orderByDesc('id')
                        ->first(['id', 'title'])?->toArray(),
                ];
            })
            : ['latestSelection' => null];

        return view('welcome', $data);
    }
}
