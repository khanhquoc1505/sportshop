<?php
namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;
use App\Models\YeuThich;
use App\Models\DanhGia;
use App\Models\DonHangSanPham;
use Illuminate\Support\Facades\Auth;

class ChiTietController extends Controller
{
    public function show($id)
{
    $product = SanPham::with([
        'variants.kichCo',
        'variants.mauSac',
        'variants',  
        'colorImages',   // chứa cả hinh_anh
        'boMons'       // belongsToMany BoMon → để lấy sản phẩm cùng môn
    ])->findOrFail($id);
        
    // 1) Thumbnails
    $thumbnails = $product->colorImages
    ->pluck('image_path')
    ->unique()
    ->map(fn($img) => asset('images/' . $img))
    ->values();

    // 2) Color variants
    // Lấy tất cả ảnh cùng màu
// 1) Tất cả ảnh (cho thumbnails), gắn cả màu
    $allColorImages = $product->colorImages->map(fn($img) => [
        'mausac_id'  => $img->mausac_id,
        'image_path' => $img->image_path,
        'url'        => asset('images/' . $img->image_path),
    ])->values();

    // 2) Danh sách swatch mỗi màu (lấy ảnh đầu làm đại diện)
    $colorVariants = $product->colorImages
        ->groupBy('mausac_id')
        ->map(fn($group, $mausac_id) => [
            'mausac_id' => $mausac_id,
            'mausac'    => $group->first()->mauSac->mausac,
            'image_url' => asset('images/' . $group->first()->image_path),
        ])
        ->values();

    // 3) Sizes
    $sizes = $product->variants
        ->pluck('kichCo')
        ->unique('id')
        ->values();

    // 4) Related: lấy 8 sản phẩm khác cùng bộ môn đầu tiên
    $related = collect();
    if ($bm = $product->boMons->first()) {
        $related = $bm->sanPhams()
    ->with('avatarImage')
    ->where('sanpham.id', '<>', $product->id)
    ->limit(8)
    ->get();
    }

    return view('layouts.chitiet', compact(
        'product', 'thumbnails','allColorImages', 'colorVariants', 'sizes', 'related'
    ));
}

    public function moTa($id)
{
    $product = SanPham::findOrFail($id);
    return view('layouts.mota', compact('product'));
}

    public function add(Request $request, SanPham $product)
    {
        $quantity = $request->input('quantity', 1);
        // Ví dụ: lưu vào session
        $cart = session()->get('cart', []);
        $cart[$product->id] = [
          'product'  => $product,
          'quantity' => $quantity,
        ];
        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng');
    }

    public function toggle(SanPham $product)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Kiểm tra đã yêu thích chưa
        $exists = YeuThich::where('nguoidung_id', $user->id)
                         ->where('sanpham_id', $product->id)
                         ->exists();

        if ($exists) {
            // Xóa yêu thích
            YeuThich::where('nguoidung_id', $user->id)
                    ->where('sanpham_id', $product->id)
                    ->delete();
            $message = 'Đã bỏ yêu thích.';
        } else {
            // Tạo mới yêu thích
            YeuThich::create([
                'nguoidung_id'  => $user->id,
                'sanpham_id'    => $product->id,
                // 'thoi_gian_them' sẽ dùng useCurrent() nếu migration có
            ]);
            $message = 'Đã thêm vào yêu thích.';
        }

        return back()->with('success', $message);
    }

    

}
