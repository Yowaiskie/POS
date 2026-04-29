<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireActiveShift
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $position = strtolower($user->position ?? '');

        // Admins and Kitchen staff don't need cash register shifts
        if ($position === 'admin' || $position === 'kitchen') {
            return $next($request);
        }

        if (!$user->activeShift) {
            if (!$request->routeIs('shifts.*') && !$request->routeIs('logout') && !$request->routeIs('profile.*')) {
                return redirect()->route('shifts.prompt');
            }
        }

        return $next($request);
    }
}
