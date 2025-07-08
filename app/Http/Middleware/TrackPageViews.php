<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrackPageViews
{
    public function handle($request, Closure $next)
    {
        // Chỉ tính lượt truy cập cho frontend (không đếm API, admin, CSS/JS...)
        if ($request->is('/')) {
            $today = Carbon::today()->toDateString();

            DB::table('page_views')
              ->updateOrInsert(
                ['visited_at' => $today],
                ['count'      => DB::raw('count + 1')]
              );
        }

        return $next($request);
    }
}
