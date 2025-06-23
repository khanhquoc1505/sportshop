<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DonHang;
use App\Models\DanhGia;

class CTDonHangController extends Controller
{
    // danh sách đơn đã hoàn thành
    public function donhang()
{
    $user = Auth::user();
    if (! $user) return redirect()->route('login');

    $orders = DonHang::with('chiTiet.sanPham')
        ->where('nguoidung_id', $user->id)
        ->where('trangthai', 2)
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
        ->where('trangthai', 2)
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
}




