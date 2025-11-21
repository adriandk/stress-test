<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class KonselorMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->account_type !== 'konselor') {
            abort(403, 'Akses khusus Konselor.');
        }

        return $next($request);
    }
}