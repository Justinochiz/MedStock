<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            Auth::user()->timestamps = false;
            Auth::user()->update(['last_seen_at' => now()]);
            Auth::user()->timestamps = true;
        }

        return $next($request);
    }
}
