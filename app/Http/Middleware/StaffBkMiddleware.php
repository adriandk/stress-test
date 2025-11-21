<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class StaffBkMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->account_type !== 'staff_bk') {
            abort(403, 'Akses khusus Staff BK.');
        }

        return $next($request);
    }
}