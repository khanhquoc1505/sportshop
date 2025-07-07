<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DonHang;
use App\Models\DanhGia;
use Illuminate\Support\Facades\DB; 

class CTDonHangController extends Controller
{
    // danh sách đơn đã hoàn thành
    public function donhang()
{
    $user = Auth::user();
    if (! $user) return redirect()->route('login');

    $orders = DonHang::with('chiTiet.sanPham')
        ->where('nguoidung_id', $user->id)
        ->whereIn('trangthai', [0, 2, 3])
        ->latest()
        ->get();

    // vì file nằm layouts/donhang.blade.php
    return view('layouts.donhang', compact('orders'));
}

public function show($id)
{
    $user = Auth::user();
    if (! $user) return redirect()->route('login');

    $order = DonHang::with('chiTiet.sanPham')
        ->where('id', $id)
        ->where('nguoidung_id', $user->id)
        ->whereIn('trangthai', [0, 2, 3])
        ->firstOrFail();

    // vì file nằm layouts/chitietdonhang.blade.php
    return view('layouts.chitietdonhang', compact('order'));
}

// đánh giá
public function store(Request $request)
    {
        // 1) Validate input
        $data = $request->validate([
            'san_pham_id' => 'required|exists:sanpham,id',
            'sosao'       => 'required|integer|min:1|max:5',
            'noi_dung'    => 'required|string',
            'hinh_anh.*'  => 'nullable|image|max:2048',
        ]);

        // 2) Xử lý upload hình
        $paths = [];
        if ($request->hasFile('hinh_anh')) {
            foreach ($request->file('hinh_anh') as $file) {
                $paths[] = $file->store('reviews', 'public');
            }
        }

        // 3) Tạo record
        DanhGia::create([
            'nguoidung_id'  => Auth::id(),
            'san_pham_id'   => $data['san_pham_id'],
            'sosao'         => $data['sosao'],
            'noi_dung'      => $data['noi_dung'],
            'hinh_anh'      => $paths,
            'ngaydanhgia'   => now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá!');
    }

   public function cancel(Request $request, $id)
    {
        // 1) Kiểm quyền: chỉ chủ đơn mới được hủy
        if (! Auth::check()) {
            abort(403);
        }

        $order = DonHang::find($id);
        if (! $order || $order->nguoidung_id !== Auth::id()) {
            return back()->with('error', 'Đơn không tồn tại hoặc không phải của bạn.');
        }

        // 2) Chỉ hủy khi trạng thái hiện tại == 2
        if ((int)$order->trangthai !== 2) {
            return back()->with('error', 'Chỉ đơn đã thanh toán mới được hủy.');
        }

        // 3) Cập nhật trực tiếp
        DB::table('donhang')
            ->where('id', $id)
            ->where('nguoidung_id', Auth::id())
            ->update([
                'trangthai'        => 0,
                'trangthaidonhang' => 'huy',
            ]);

        // 4) Redirect về chi tiết kèm thông báo
        return redirect()
            ->route('donhang.show', $id)
            ->with('success', 'Đã hủy đơn hàng thành công.');
    }
}