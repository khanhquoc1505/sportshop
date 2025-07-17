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
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChangeCodeMail;
use App\Services\OtpService; // Service chung

class HomeController extends Controller
{
    
    //HEADER
    public function index()
    {
        // Lấy products kèm ảnh đại diện
        $products = SanPham::with('avatarImage')->get();
        //$products = SanPham::with('avatarImage')->paginate(8);
        // Nếu bạn cần tabs theo bộ môn
        $bomons   = BoMon::with('sanPhams')->get();

        return view('layouts.chinh', compact('products','bomons'));
    
    }

    // 1️⃣ Hiển thị form đăng ký
    public function showRegisterForm()
    {
        return view('layouts.dangky');
    }

    // 2️⃣ Xử lý form đăng ký: validate, lưu session tạm và gửi OTP
    public function processRegister(Request $request)
    {
        $validator = Validator::make(
        $request->all(),
        [
            'ten_nguoi_dung' => 'required|string|max:255',
            'email'          => 'required|email|unique:nguoidung,email',
            'sdt'            => 'required|digits:10|unique:nguoidung,sdt',
            'dia_chi'        => 'nullable|string|max:255',
            'mat_khau'       => 'required|string|min:6|confirmed',
        ],
        // ——— Các thông báo tiếng Việt ———
        [
            'ten_nguoi_dung.required' => 'Bạn phải nhập họ và tên.',
            'ten_nguoi_dung.string'   => 'Họ và tên không hợp lệ.',
            'ten_nguoi_dung.max'      => 'Họ và tên không được vượt quá :max ký tự.',

            'email.required'          => 'Bạn phải nhập email.',
            'email.email'             => 'Email không đúng định dạng.',
            'email.unique'            => 'Email đã được sử dụng.',

            'sdt.required'            => 'Bạn phải nhập số điện thoại.',
            'sdt.digits'              => 'Số điện thoại phải gồm đúng :digits chữ số.',
            'sdt.unique'              => 'Số điện thoại đã được sử dụng.',

            'dia_chi.string'          => 'Địa chỉ không hợp lệ.',
            'dia_chi.max'             => 'Địa chỉ không được vượt quá :max ký tự.',

            'mat_khau.required'       => 'Bạn phải nhập mật khẩu.',
            'mat_khau.string'         => 'Mật khẩu không hợp lệ.',
            'mat_khau.min'            => 'Mật khẩu phải có ít nhất :min ký tự.',
            'mat_khau.confirmed'      => 'Xác nhận mật khẩu không khớp.',
        ]
    );

    // Đổi tên trường hiển thị
    $validator->setAttributeNames([
        'ten_nguoi_dung' => 'Họ và tên',
        'email'          => 'Email',
        'sdt'            => 'Số điện thoại',
        'dia_chi'        => 'Địa chỉ',
        'mat_khau'       => 'Mật khẩu',
        'mat_khau_confirmation' => 'Xác nhận mật khẩu',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

        // Lưu tạm dữ liệu đăng ký (mật khẩu đã hash)
        session(['reg_data' => [
            'ten_nguoi_dung' => $request->ten_nguoi_dung,
            'email'          => $request->email,
            'sdt'            => $request->sdt,
            'dia_chi'        => $request->dia_chi,
            'mat_khau'       => $request->mat_khau,
            'vai_tro'        => 'customer',
        ]]);

        // Tạo và lưu OTP vào session
        $otp = rand(100000, 999999);
    session([
        'reg_otp'           => $otp,
        'reg_otp_expires'   => Carbon::now()->addMinutes(10)->timestamp, // lưu timestamp
    ]);

    Mail::to($request->email)
        ->send(new PasswordChangeCodeMail($request->ten_nguoi_dung, $otp));

    return redirect()->route('dangky.confirm.form')
                     ->with('status','Mã xác nhận đã được gửi tới email của bạn.');
    }

    // 3️⃣ Hiển thị form nhập OTP
    public function showRegisterConfirm()
    {
        if (! session()->has('reg_data')) {
            return redirect()->route('dangky.form')
                             ->withErrors(['otp' => 'Yêu cầu đăng ký đã hết hạn.']);
        }
        return view('layouts.xacnhan');
    }

    // 4️⃣ Xác nhận OTP và tạo tài khoản
    public function confirmRegister(Request $request)
{
    $request->validate(['otp' => 'required|digits:6']);

    $expires = session('reg_otp_expires');
    $nowTs   = Carbon::now()->timestamp;

    if (! $expires || $nowTs > $expires || session('reg_otp') != $request->otp) {
        return back()->withErrors(['otp' => 'Mã không đúng hoặc đã hết hạn.']);
    }

    // Tạo user từ session('reg_data')
    NguoiDung::create(session('reg_data'));

    // Xóa session tạm
    session()->forget(['reg_data','reg_otp','reg_otp_expires']);

    return redirect()->route('login')
                     ->with('success','Đăng ký thành công! Vui lòng đăng nhập.');
}

public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'mat_khau' => 'required|string',
        ]);

        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'sdt';
        $user  = NguoiDung::where($field, $request->login)->first();

        if (! $user || $user->mat_khau !== $request->mat_khau) {
            return back()
                ->withErrors(['login'=>'Email/SĐT hoặc mật khẩu không đúng'])
                ->withInput();
        }

        Auth::login($user, $request->has('remember'));
         if ($data = session()->pull('pending_buy_now')) {
        return redirect()->route('cart.buynow', $data);
    }
        // không có pending, fallback về intended (nếu bạn có dùng)
        return redirect()->intended('/');
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
    // Bước 2: Hiển thị và xử lý form nhập email/SĐT
    // 1️⃣ Form nhập email/SĐT
public function showForgotForm()
{
    return view('layouts.forgot-password');
}

public function processForgot(Request $request)
{
    $request->validate(['login'=>'required']);
    $user = NguoiDung::where('email',$request->login)
                    ->orWhere('sdt',$request->login)
                    ->first();
    if (!$user) {
        return back()->withErrors(['login'=>'Không tìm thấy tài khoản.']);
    }
    session(['reset_login' => $request->login]);
    return redirect()->route('password.change.form');
}

// 2️⃣ Form nhập mật khẩu mới
public function showChangeForm()
{
    if (!session('reset_login')) {
        return redirect()->route('password.request');
    }
    return view('layouts.change-password');
}

public function sendResetCode(Request $request)
{
    $request->validate([
        'new_password' => 'required|min:6|confirmed'
    ],[
        'new_password.confirmed'=>'Xác nhận mật khẩu không khớp.'
    ]);

    //session(['new_hashed' => Hash::make($request->new_password)]);
    session(['new_plain_password' => $request->new_password]);

    $code = rand(100000, 999999);
    session(['otp_code' => $code]);

    $user = NguoiDung::where('email', session('reset_login'))
                    ->orWhere('sdt', session('reset_login'))
                    ->first();

    Mail::to($user->email)
        ->send(new PasswordChangeCodeMail($user->ten_nguoi_dung, $code));

    return redirect()->route('password.confirm.form')
                     ->with('status','Mã xác nhận đã được gửi về email của bạn.');
}

// 3️⃣ Form nhập OTP
public function showConfirmForm()
{
    if (!session('reset_login') || !session('new_plain_password')) {
        return redirect()->route('password.request');
    }
    return view('layouts.confirm-code');
}

public function confirmReset(Request $request)
{
    $request->validate(['code'=>'required|digits:6']);

    if ($request->code != session('otp_code')) {
        return back()->withErrors(['code'=>'Mã xác nhận không đúng.']);
    }

    $user = NguoiDung::where('email', session('reset_login'))
                    ->orWhere('sdt', session('reset_login'))
                    ->first();
    //$user->mat_khau = session('new_hashed');
    $user->mat_khau = session('new_plain_password');
    $user->save();

    session()->forget(['reset_login','new_hashed','otp_code']);

    return redirect()->route('login')
                     ->with('status','Đổi mật khẩu thành công, vui lòng đăng nhập lại.');
}
}
