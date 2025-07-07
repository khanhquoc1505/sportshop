<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SanPham;

class WishlistController extends Controller
{
    public function toggle(SanPham $product)
    {
        $user = Auth::user();

        // Tìm bản ghi yeuthich nếu đã tồn tại
        $fav = $user->yeuThichs()
                    ->where('sanpham_id', $product->id)
                    ->first();

        if ($fav) {
            // xoá nếu đã yêu thích
            $fav->delete();
        } else {
            // thêm nếu chưa
            $user->yeuThichs()->create([
                'sanpham_id'   => $product->id,
                'thoi_gian_them' => now(),
            ]);
        }

        return back();
    }
}
