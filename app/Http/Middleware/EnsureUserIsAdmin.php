<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        if (Auth::user()->vai_tro !== 'admin') {
            return redirect()->route('home')->with('error', 'Bạn không đủ quyền.');
        }

        return $next($request);
    }
}
