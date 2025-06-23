<?php

namespace App\Http\Controllers;
use App\Models\SanPham;
use App\Models\DonHangSanPham;
use App\Models\DonHang;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;



use Illuminate\Http\Request;

class AddGioHangController extends Controller
{
    
     public function themgiohang(Request $request, SanPham $product)
{
    $user = auth()->user();
    if (!$user) return redirect()->route('login')->with('error', 'Bạn cần đăng nhập');

    $donHang = DonHang::where('nguoidung_id', $user->id)
        ->where('trangthai', 1)
        ->first();

    if (!$donHang) {
        $donHang = DonHang::create([
            'nguoidung_id' => $user->id,
            'trangthai' => 1,
            'madon' => 'DH' . now()->format('YmdHis') . Str::random(3),
            'trangthaidonhang' => 'chuadathang',
            'tongtien' => 0,
        ]);
    }

    $size = $request->input('size');
    $color = $request->input('mausac');
    $image = $request->input('hinh_anh');

    $chiTiet = DonHangSanPham::where('donhang_id', $donHang->id)
        ->where('sanpham_id', $product->id)
        ->where('size', $size)
        ->where('mausac', $color)
        ->first();

    if ($chiTiet) {
        $chiTiet->soluong += $request->quantity;
        $chiTiet->save();
    } else {
        DonHangSanPham::create([
            'donhang_id' => $donHang->id,
            'sanpham_id' => $product->id,
            'soluong' => $request->quantity,
            'dongia' => $product->gia_ban,
            'size' => $size,
            'mausac' => $request->mausac,
            'hinh_anh' => $request->hinh_anh,
        ]);
    }

    return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng');
}


public function showgiohang(Request $request)
{
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }

    $donhang = DonHang::where('nguoidung_id', $user->id)
        ->where('trangthai', 1)
        ->with(['chiTiet.sanPham', 'chiTiet.kichCo', 'chiTiet.mauSac'])
        ->first();

    if (!$donhang || $donhang->chiTiet->isEmpty()) {
        return view('layouts.giohang', [
            'donhang'           => $donhang,
            'user'              => $user,
            'availableVouchers' => collect(),
            'voucherGiam'       => 0,
            'tongSau'           => 0,
            'phiGiaoHang'       => 0,
            'tongCuoi'          => 0,
        ]);
    }

    $tongGoc = 0;
    $tongKmSP = 0;

    // 1) Tính subtotal + khuyến mãi SP
    foreach ($donhang->chiTiet as $item) {
        $subtotal = $item->soluong * $item->dongia;
        $promo = $item->sanPham->vouchers()
            ->wherePivot('discount_percent','>',0)
            ->where('ngay_bat_dau','<=', now())
            ->where('ngay_ket_thuc','>=', now())
            ->first();

        $kmsp = $promo
            ? $subtotal * ($promo->pivot->discount_percent/100)
            : 0;

        $item->item_discount = $kmsp;
        $item->item_total    = $subtotal - $kmsp;

        $tongGoc  += $subtotal;
        $tongKmSP += $kmsp;
    }

    // 2) Lấy voucher đơn hàng thỏa điều kiện min_order_value
    $availableVouchers = Voucher::where('ngay_bat_dau','<=', now())
        ->where('ngay_ket_thuc','>=', now())
        ->where('min_order_value','<=', $tongGoc)
        ->get();

    // 3) Xử lý voucher được chọn
    $selectedVoucher = null;
    $voucherGiam = 0;
    if ($vid = $request->query('order_voucher')) {
        $selectedVoucher = Voucher::find($vid);
        if ($selectedVoucher) {
            $base = $tongGoc - $tongKmSP;
            if ($selectedVoucher->loai === 'percent') {
                $voucherGiam = $base * ($selectedVoucher->soluong/100);
            } else {
                $voucherGiam = $selectedVoucher->soluong;
            }
            // không vượt quá số tiền còn lại
            $voucherGiam = min($voucherGiam, $base);
        }
    }

    // 4) Tính tổng sau giảm và phí ship
    $tongSau     = $tongGoc - $tongKmSP - $voucherGiam;
    $phiGiaoHang = $tongSau >= 300000 ? 0 : 19000;
    $tongCuoi    = $tongSau + $phiGiaoHang;

    return view('layouts.giohang', compact(
        'donhang','user','availableVouchers','selectedVoucher',
        'voucherGiam','tongSau','phiGiaoHang','tongCuoi'
    ));
}
public function update(Request $request, $id)
{
    $item = DonHangSanPham::findOrFail($id);
    if ($request->input('action') === 'increase') {
        $item->soluong++;
    } elseif ($request->input('action') === 'decrease') {
        $item->soluong = max(1, $item->soluong - 1);
    }
    $item->save();
    return back();
}

public function remove($id)
{
    DonHangSanPham::destroy($id);
    return back();
}

public function thanhtoan(Request $request)
{
    $user = auth()->user();
    if (! $user) {
        return redirect()->route('login');
    }

    // Lấy đơn hàng đang ở trạng thái giỏ (1)
    $donhang = DonHang::where('nguoidung_id', $user->id)
        ->where('trangthai', 1)
        ->with('chitiet')
        ->first();

    if (! $donhang || $donhang->chitiet->isEmpty()) {
        return redirect()->back()->with('error', 'Không có đơn hàng nào để thanh toán!');
    }

    // 1) Tính tổng gốc của đơn
    $tong = $donhang->chitiet->sum(function ($ct) {
        return $ct->soluong * $ct->dongia;
    });

    // 2) Tính voucher giảm (lấy id từ hidden input)
    $voucherGiam = 0;
    $vid = $request->input('order_voucher');
    if ($vid) {
        $voucher = Voucher::where('id', $vid)
            ->where('ngay_bat_dau', '<=', now())
            ->where('ngay_ket_thuc','>=', now())
            ->where('min_order_value','<=', $tong)
            ->first();

        if ($voucher) {
            if ($voucher->loai === 'percent') {
                $voucherGiam = $tong * ($voucher->soluong / 100);
            } else {
                $voucherGiam = $voucher->soluong;
            }
            // không để âm hoặc vượt tổng
            $voucherGiam = min($voucherGiam, $tong);
        }
    }

    // 3) Cập nhật đơn hàng
    $donhang->tongtien        = $tong;
    $donhang->gia_giam        = $voucherGiam;
    $donhang->trangthai       = 2;
    $donhang->trangthaidonhang = 'dadathang';
    $donhang->save();

    return redirect()->route('cart.success')
                     ->with('success', 'Thanh toán thành công! Tổng giảm: '.number_format($voucherGiam).'₫');
}

}
