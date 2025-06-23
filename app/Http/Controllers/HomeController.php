<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\NguoiDung;
use App\Models\BoMon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
         
    
        // Lấy products kèm ảnh đại diện
        $products = SanPham::with('avatarImage')->get();

        // Nếu bạn cần tabs theo bộ môn
        $bomons   = BoMon::with('sanPhams')->get();

        return view('layouts.chinh', compact('products','bomons'));
    
    }

    public function dangky(Request $request)
{
    $validator = Validator::make($request->all(), [
        'ten_nguoi_dung' => 'required|string|max:255',
        'email' => 'nullable|email|unique:nguoidung,email',
        'sdt' => 'nullable|digits:10|unique:nguoidung,sdt',
        'dia_chi' => 'nullable|string|max:255',
        'mat_khau' => 'required|string|min:6|confirmed',
    ], [
        'email.unique' => 'Email đã tồn tại trong hệ thống.',
        'sdt.unique' => 'Số điện thoại đã tồn tại trong hệ thống.',
        'mat_khau.confirmed' => 'Mật khẩu xác nhận không khớp.',
        'mat_khau.min' =>'mật khẩu không ít hơn 6'
    ]);

    // ✨ Gắn nhãn tiếng Việt cho các field
    $validator->setAttributeNames([
        'ten_nguoi_dung' => 'họ và tên',
        'email' => 'email',
        'sdt' => 'số điện thoại',
        'dia_chi' => 'địa chỉ',
        'mat_khau' => 'mật khẩu',
        'mat_khau_confirmation' => 'xác nhận mật khẩu',
    ]);

    if (!$request->email && !$request->sdt) {
        return back()->withErrors(['email' => 'Phải nhập ít nhất Email hoặc Số điện thoại.'])->withInput();
    }

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    NguoiDung::create([
        'ten_nguoi_dung' => $request->ten_nguoi_dung,
        'email' => $request->email,
        'sdt' => $request->sdt,
        'dia_chi' => $request->dia_chi,
        'mat_khau' => $request->mat_khau,
        'vai_tro' => 'customer',
    ]);

    return redirect()->route('login.form')->with('success', 'Đăng ký thành công!');
}

public function login(Request $request)
{
    $request->validate([
        'login' => 'required',
        'mat_khau' => 'required|string',
    ]);

    $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'sdt';

    // Tìm người dùng theo email hoặc sdt
    $nguoidung = NguoiDung::where($login_type, $request->login)->first();

    // So sánh mật khẩu đúng y như người dùng nhập (không mã hóa)
    if ($nguoidung && $nguoidung->mat_khau === $request->mat_khau) {
        // Ghi nhớ nếu người dùng chọn "remember"
        auth()->login($nguoidung, $request->has('remember'));

        return redirect()->route('layouts.chinh');
    }

    return back()->withErrors([
        'login' => 'Sai thông tin đăng nhập',
    ]);
}

public function logout(Request $request)
{
    Auth::logout(); // đăng xuất user hiện tại

    // Xoá session nếu cần
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/'); // về trang chủ
}

}
