<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || $user->vai_tro !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }
        return $next($request);
    }
}
