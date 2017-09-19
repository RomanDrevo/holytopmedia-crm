<?php

namespace App\Http\Middleware;

use Closure;

class UserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if ($request->user()->hasRole($role) || $request->user()->hasRole("app_admin")) {
            return $next($request);
        }

        return redirect()->back()->with("error", "You don't have permission to access this route.");
    }
}
