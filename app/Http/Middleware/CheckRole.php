<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $userGroupId = $request->user()->group_id;
        
        // Convert string roles to integers for comparison
        $allowedRoles = array_map('intval', $roles);
        
        if (!in_array($userGroupId, $allowedRoles)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            return redirect('/my-tasks')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}