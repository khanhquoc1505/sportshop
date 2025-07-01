<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\NguoiDung;
use App\Models\BoMon;
use App\Models\Loai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    //HEADER
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

////////////////////////////////////
// 1. Hiển thị form nhập email/SĐT
    public function showForgotForm()
    {
        return view('layouts.forgot-password');
    }

    // Bước 2: Xử lý form forgot, kiểm tra login, lưu session, redirect
    public function sendForgot(Request $request)
    {
        $request->validate(['login'=>'required']);

        $user = NguoiDung::where('email',$request->login)
                        ->orWhere('sdt',$request->login)
                        ->first();

        if (! $user) {
            return back()->withErrors(['login'=>'Không tìm thấy tài khoản.']);
        }

        // Lưu login vào session để dùng ở bước reset
        session([
            'reset_login' => $request->login,
        ]);

        return redirect()->route('password.reset');
    }

    // Bước 3: Show form reset, sinh mã gacha text-only và lưu vào session
    public function showResetForm()
    {
        $login = session('reset_login','');
        if (! $login) {
            return redirect()->route('password.request');
        }

        // Sinh mã gacha 6 ký tự (chữ HOA + số), lưu session
        $plainCaptcha = Str::upper(Str::random(6));
        session(['plain_captcha' => $plainCaptcha]);

        return view('layouts.reset-password', [
            'login'        => $login,
            'plainCaptcha' => $plainCaptcha,
        ]);
    }

    // Bước 4: Xử lý submit đổi mật khẩu
    public function resetPassword(Request $request)
    {
        $request->validate([
            'login'                    => 'required',
            'code'                     => 'required|string|size:6',
            'new_password'             => 'required|min:6|confirmed',
        ]);

        // Kiểm tra login khớp session
        if ($request->login !== session('reset_login')) {
            return back()->withErrors(['login'=>'Có lỗi, vui lòng thử lại.']);
        }

        // Kiểm tra mã gacha
        if (strtoupper($request->code) !== session('plain_captcha')) {
            return back()->withErrors(['code'=>'Mã gacha không đúng.']);
        }

        // Tìm user và đổi mật khẩu
        $user = NguoiDung::where('email',$request->login)
                        ->orWhere('sdt',$request->login)
                        ->first();

        //$user->mat_khau = Hash::make($request->new_password);
        $user->mat_khau = $request->new_password;
        $user->save();

        // Xóa session tạm
        session()->forget(['reset_login','plain_captcha']);

        return redirect()->route('login')
                         ->with('status','Đổi mật khẩu thành công, vui lòng đăng nhập lại.');
    }

//tìm kiếm
public function autocomplete(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $products = SanPham::with('avatarImage')
            ->where('ten', 'like', "%{$q}%")
            ->orWhereHas('bomons', function($qb) use($q) {
                $qb->where('bomon', 'like', "%{$q}%");
            })
            ->take(5)
            ->get();

        $results = $products->map(function($p) {
            return [
                'url' => route('product.show', $p->id),
                'img' => asset('images/' . optional($p->avatarImage)->image_path ?: 'default.jpg'),
                'ten' => $p->ten,
                'gia' => number_format($p->gia_ban,0,',','.'),
            ];
        });

        return response()->json($results);
    }
}
