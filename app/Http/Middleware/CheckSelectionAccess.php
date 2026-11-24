<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSelectionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $selection = $request->route('selection');
        $user = $request->user();

        $isOwner = $selection->owner_id == $user->id;
        $hasSharedAccess = ! $isOwner && $selection->users()->whereKey($user->id)->exists();

        if (! $isOwner && ! $hasSharedAccess) {
            abort(403);
        }

        return $next($request);
    }
}
