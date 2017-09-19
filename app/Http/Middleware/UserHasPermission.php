<?php

namespace App\Http\Middleware;

use Closure;

class UserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if ($request->user()->hasPermission($permission) ) {
            return $next($request);
        }
        return redirect()->back()->with("error", "You don't have permission to access this route.");
    }
}
