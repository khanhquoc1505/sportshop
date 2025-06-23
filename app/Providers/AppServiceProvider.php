<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\DonHang;
use App\Models\Loai;
use App\Models\Bomon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Chia sẻ menu loại và bộ môn cho tất cả view
        View::share('loais', Loai::where('status', 1)->get());
        View::share('bomons', Bomon::all());

        // Composer để chia sẻ giỏ hàng (đơn hàng đang mở) cho tất cả view
        View::composer('*', function ($view) {
            $user = auth()->user();
            if ($user) {
                $donhang = DonHang::with('chiTiet')
                    ->where('nguoidung_id', $user->id)
                    ->where('trangthai', 1)
                    ->first();
            } else {
                $donhang = null;
            }

            $view->with('donhang', $donhang);
        });
    }
}
