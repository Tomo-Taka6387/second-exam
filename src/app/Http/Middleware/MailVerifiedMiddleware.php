<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MailVerifiedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $session_data = session()->get('unauthenticated_user') ?? null;

        if ($session_data) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
