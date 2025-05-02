<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CoordinatorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        if (!$user || !$user->group_id) {
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        // Allow admin (group_id = 1), secretary (group_id = 2), and coordinator (group_id = 4)
        if ($user->group_id === 1 || $user->group_id === 2 || $user->group_id === 4) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Unauthorized access.');
    }
}