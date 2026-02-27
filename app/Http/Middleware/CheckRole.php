<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Verify the authenticated user belongs to one of the allowed roles/guards.
     *
     * Usage in routes: middleware('role:moh'), middleware('role:midwife,parent')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        foreach ($roles as $role) {
            if (Auth::guard($role)->check()) {
                return $next($request);
            }
        }

        // Unauthorized — return JSON for AJAX, redirect for web requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 0,
                'msg' => 'Unauthorized. You do not have permission to access this resource.'
            ], 403);
        }

        session()->flash('fail', 'Unauthorized. You do not have permission to access this page.');

        // Redirect to the most appropriate login page
        if (in_array('moh', $roles)) {
            return redirect()->route('moh.login');
        } elseif (in_array('midwife', $roles)) {
            return redirect()->route('midwife.login');
        } elseif (in_array('parent', $roles)) {
            return redirect()->route('parent.login');
        }

        return redirect('/');
    }
}
