<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string ...$guards
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        if (empty($guards)) {
            $guards = ['admin', 'web'];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($guard === 'admin' && $user->role !== 'admin') {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login');
                }

                return $next($request);
            }
        }

        return redirect()->route('login');
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */

    protected function redirectTo($request)
    {
        if ($request->is('admin/*')) {
            return route('admin.login');
        }

        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
