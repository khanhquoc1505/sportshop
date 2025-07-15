<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\DonHang;
use App\Models\Voucher;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;

class CSThongTinController extends Controller
{
    // === 1. Hồ sơ (Profile) ===
    public function profile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }
     public function favorites()
{
    // Lấy user
    $user = Auth::user();

    // Lấy sản phẩm yêu thích, kèm ảnh chính
    $favorites = $user
        ->favorites()
        ->with('avatarImage')    // eager load ảnh chính
        ->get();

    // Đưa lên view
    return view('account.favorites', compact('favorites'));
}
public function removeFavorite($productId)
    {
        $user = Auth::user();

        // Nếu dùng quan hệ ManyToMany (pivot), detach productId
        $user->favorites()->detach($productId);

        // Chuyển về lại trang favorites kèm flash message
        return redirect()
            ->route('favorites.index')
            ->with('success', 'Đã xóa sản phẩm khỏi yêu thích.');
    }

    public function updateProfile(Request $request)
{
    $user = Auth::user();

    $data = $request->validate([
        'ten_nguoi_dung' => 'required|string|max:255|unique:nguoidung,ten_nguoi_dung,'.$user->id,
        'email'          => 'required|email|unique:nguoidung,email,'.$user->id,
        'sdt'            => 'required|string|unique:nguoidung,sdt,'.$user->id,
        'dia_chi'        => 'nullable|string|max:255',
        'avatar'         => 'nullable|image|max:1024', // 1MB
    ]);

    if ($request->hasFile('avatar')) {
        // Xóa file cũ nếu có
        if ($user->avatar && File::exists(public_path($user->avatar))) {
            File::delete(public_path($user->avatar));
        }

        // Lưu file vào public/uploads/avatars
        $file      = $request->file('avatar');
        $filename  = time().'_'.$file->getClientOriginalName();
        $dir       = 'uploads/avatars';
        $file->move(public_path($dir), $filename);

        // Ghi path relative vào DB
        $data['avatar'] = $dir.'/'.$filename;
    }

    $user->update($data);

    return back()->with('success', 'Cập nhật hồ sơ thành công.');
}

    // đổi Email
    public function changeEmail()
    {
        return view('account.change-email');
    }
    public function updateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:nguoidung,email,'.Auth::id()]);
        Auth::user()->update(['email' => $request->email]);
        return back()->with('success', 'Đã cập nhật email.');
    }

    // đổi Phone
    public function changePhone()
    {
        return view('account.change-phone');
    }
    public function updatePhone(Request $request)
    {
        $request->validate(['phone' => 'required|string|unique:nguoidung,sdt,'.Auth::id()]);
        Auth::user()->update(['sdt' => $request->phone]);
        return back()->with('success', 'Đã cập nhật số điện thoại.');
    }

    // đổi Birthdate
    public function changeBirthdate()
    {
        return view('account.change-birthdate');
    }
    public function updateBirthdate(Request $request)
    {
        $request->validate(['birthdate' => 'required|date']);
        Auth::user()->update(['birthdate' => $request->birthdate]);
        return back()->with('success', 'Đã cập nhật ngày sinh.');
    }

    // === 2. Đơn mua (Orders) ===
    public function ordersIndex()
    {
        $orders = DonHang::with('chiTiet.sanPham')
            ->where('nguoidung_id', Auth::id())
            ->latest()
            ->get();

        return view('account.orders.index', compact('orders'));
    }

    public function orderShow($id)
    {
        $order = DonHang::with('chiTiet.sanPham')
            ->where('nguoidung_id', Auth::id())
            ->findOrFail($id);

        return view('account.orders.show', compact('order'));
    }

    public function orderCancel($id)
    {
        $order = DonHang::where('nguoidung_id', Auth::id())
                        ->findOrFail($id);

        if (! in_array($order->trangthaidonhang, ['chuadathang','dathanhtoan'])) {
            return back()->with('error', 'Không thể hủy đơn này.');
        }

        $order->update(['trangthaidonhang' => 'huy']);

        return back()->with('success', 'Đã hủy đơn hàng.');
    }

    // === 3. Kho voucher ===
    public function vouchers()
    {
        $vouchers = Voucher::whereHas('nguoiDungVoucher', fn($q) =>
            $q->where('nguoidung_id', Auth::id())
        )->get();

        return view('account.vouchers', compact('vouchers'));
    }

    // === 4. Shopee Xu (Coins) ===
    public function coins()
    {
        $coins = Auth::user()->coins ?? 0;
        return view('account.coins', compact('coins'));
    }
    // Hiển thị form đổi mật khẩu
    public function showChangePasswordForm()
    {
        return view('account.change-password');
    }

    // Xử lý đổi mật khẩu (chỉ new_password và confirmation)
    public function changePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        // Cập nhật trực tiếp
        Auth::user()->update([
            'mat_khau' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }
}
