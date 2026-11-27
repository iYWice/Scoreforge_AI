<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Represents the RoleMiddleware application component.
 */
class RoleMiddleware
{
    /**
     * Handles handle.
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // FIRST: check if logged in
        if (! Auth::check()) {
            return redirect('/login');
        }

        // SECOND: check role safely
        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
