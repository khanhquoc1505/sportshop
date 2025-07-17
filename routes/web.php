<?php

use App\Http\Controllers\TiemKiemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChiTietController;
use App\Http\Controllers\CTDonHangController;
use App\Http\Controllers\AddGioHangController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CSThongTinController;
use App\Models\SanPham;
use App\Models\BoMon;



//////////////
//Route::get('/san-pham/tim-kiem', [TiemKiemController::class, 'search'])->name('product.search');
Route::get('/product/search', [TiemKiemController::class, 'search'])->name('product.search');
Route::get('/product/autocomplete', [TiemKiemController::class, 'autocomplete'])->name('product.autocomplete');
Route::view('layouts/timkiemSP', 'layouts.timkiem')
     ->name('layouts.timkiemSP');
//////////////
Route::get('/', function () {
    return view('home/trangchu');
});

// 1) Form Đăng ký (GET)
Route::get('/dangky', [HomeController::class, 'showRegisterForm'])
     ->name('dangky.form');

// 2) Xử lý gửi OTP (POST)
Route::post('/dangky', [HomeController::class, 'processRegister'])
     ->name('dangky.process');

// 3) Form nhập OTP (GET)
Route::get('/dangky/xac-nhan', [HomeController::class, 'showRegisterConfirm'])
     ->name('dangky.confirm.form');

// 4) Xác nhận OTP và tạo tài khoản (POST)
Route::post('/dangky/xac-nhan', [HomeController::class, 'confirmRegister'])
     ->name('dangky.confirm');



Route::get('/', function () {
    $products = SanPham::with('avatarImage')->paginate(8);
    $bomons = BoMon::with('sanPhams')->get();
    return view('layouts.chinh', compact('products', 'bomons'));
})->name('layouts.chinh');

Route::get('chitiet/{id}/{slug?}', [ChiTietController::class, 'show'])->name('product.show');
Route::post('/cart/add/{product}', [ChiTietController::class, 'add'])->name('cart.add');
Route::middleware('auth')->group(function () {
    Route::post('/wishlist/toggle/{product}', [ChiTietController::class, 'toggle'])->name('wishlist.toggle');
});
Route::post('/product/{product}/danhgia', [ChiTietController::class, 'guidanhgia'])
    ->name('product.review.store');
Route::get('product/{id}/mo-ta', [ChiTietController::class, 'moTa'])
    ->name('product.mo_ta');
    ///////////////////////////////
Route::post('/yeu-thich/{product}/toggle', [WishlistController::class, 'toggle'])
     ->name('wishlist.toggle')
     ->middleware('auth');
     ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
Route::middleware('auth')->group(function () {
    // Hiển thị hồ sơ và form sửa hồ sơ
    Route::get('/profile',           [CSThongTinController::class, 'profile'])
         ->name('profile.index');
    Route::get('/profile/edit',      [CSThongTinController::class, 'editProfile'])
         ->name('profile.edit');

    // Cập nhật hồ sơ (PATCH)
    Route::patch('/profile',         [CSThongTinController::class, 'updateProfile'])
         ->name('profile.update');

    // Đổi email
    Route::get('/profile/email',     [CSThongTinController::class, 'changeEmail'])
         ->name('profile.change_email');
    Route::post('/profile/email',    [CSThongTinController::class, 'updateEmail'])
         ->name('profile.update_email');

    // Đổi số điện thoại
    Route::get('/profile/phone',     [CSThongTinController::class, 'changePhone'])
         ->name('profile.change_phone');
    Route::post('/profile/phone',    [CSThongTinController::class, 'updatePhone'])
         ->name('profile.update_phone');
     Route::get('/favorites', [CSThongTinController::class, 'favorites'])
         ->name('favorites.index');
     Route::delete('/favorites/{productId}', [CSThongTinController::class, 'removeFavorite'])
         ->name('favorites.remove');
    // === Đơn mua ===
    // Xem chi tiết
    Route::get('/orders/{id}',       [CSThongTinController::class, 'orderShow'])
         ->name('orders.show');
    // Hủy đơn (PATCH)
    Route::patch('/orders/{id}/cancel',
         [CSThongTinController::class, 'orderCancel'])
         ->name('orders.cancel');
         // Hiển thị form
Route::get('/profile/password', [CSThongTinController::class, 'showChangePasswordForm'])
     ->name('profile.change_password')
     ->middleware('auth');

// Xử lý đổi mật khẩu
Route::patch('/profile/password', [CSThongTinController::class, 'changePassword'])
     ->name('profile.update_password')
     ->middleware('auth');
});
////////////////////////////////////////////////////////////////////////

//////đăng nhập đăng ký quên mật khẩu
Route::get('/dangnhap', [HomeController::class, 'showLoginForm'])->name('login');
Route::post('/dangnhap', [HomeController::class, 'login'])->name('login.post');
Route::post('/dang-xuat', [HomeController::class, 'logout'])->name('logout');
// Không cần middleware đặc biệt, hoặc dùng 'guest' nếu chỉ cho user chưa đăng nhập
Route::middleware('guest')->group(function(){
    // 1️⃣ Nhập email/SĐT
    Route::get('password/forgot', [HomeController::class, 'showForgotForm'])
         ->name('password.request');
    Route::post('password/forgot', [HomeController::class, 'processForgot'])
         ->name('password.process');

    // 2️⃣ Nhập mật khẩu mới và gửi OTP
    Route::get('password/change', [HomeController::class, 'showChangeForm'])
         ->name('password.change.form');
    Route::post('password/change', [HomeController::class, 'sendResetCode'])
         ->name('password.sendCode');

    // 3️⃣ Nhập mã OTP để xác nhận
    Route::get('password/confirm', [HomeController::class, 'showConfirmForm'])
         ->name('password.confirm.form');
    Route::post('password/confirm', [HomeController::class, 'confirmReset'])
         ->name('password.confirm');
});
////////////////////////////////////////////////////////////////////////
//chi tiết đơn hàng
Route::middleware('auth')->group(function(){
    // danh sách
    Route::get('/donhang', [CTDonHangController::class, 'donhang'])
         ->name('donhang.index');
    // chi tiết
    Route::get('/donhang/{id}/{madon}', [CTDonHangController::class, 'show'])
         ->name('donhang.show');
         // Hủy đơn (PATCH)
    
});

Route::patch('donhang/{id}/huy', [CTDonHangController::class, 'cancel'])
     ->name('donhang.cancel')
     ->middleware('auth');
// giỏ hàng
Route::match(['get','post'], 'giohang/buynow', [AddGioHangController::class, 'buynow'])
     ->name('cart.buynow');
Route::get('giohang/checkout', [AddGioHangController::class, 'checkout'])->name('cart.checkout');
Route::middleware('auth')->group(function(){
    // Hiển thị giỏ hàng
    Route::get('/giohang', [AddGioHangController::class, 'showgiohang'])
         ->name('cart.index');

    // Thêm vào giỏ
    Route::post('/giohang/them/{product}', [AddGioHangController::class, 'themgiohang'])
         ->name('cart.them');
    // Cập nhật số lượng
    Route::post('/giohang/update/{id}', [AddGioHangController::class, 'update'])
         ->name('cart.update');
    // Xóa item
    Route::delete('giohang/remove/{id}',[AddGioHangController::class,'remove'])->whereNumber('id')
          ->name('cart.remove');
     Route::post('giohang/remove/{id}',[AddGioHangController::class,'remove'])->whereNumber('id')
          ->name('cart.remove');
        
    // // Thanh toán
// POST xử lý tăng/giảm/xoá trong luồng “mua ngay”
    
    // Xử lý + / – / remove / chọn-voucher của “mua ngay”
    
    // Xử lý đặt hàng (COD hoặc VNPay)
    Route::post('/giohang/thanhtoan', [AddGioHangController::class, 'thanhtoan'])
         ->name('cart.thanhtoan');

     Route::post('/nguoidung/update-address', [App\Http\Controllers\AddGioHangController::class, 'updateAddress'])
    ->name('nguoidung.update_address');
   
    
});
// chi tiết đon hàng
Route::post('/danh-gia', [CTDonHangController::class, 'store'])
     ->name('danhgia.store')
     ->middleware('auth');

// Nhóm các route cần xác thực
Route::middleware(['web', 'auth'])->group(function () {
    // POST khởi tạo thanh toán VNPAY
    Route::post('/vnpay_payment', [AddGioHangController::class, 'vnpay_payment'])
         ->name('vnpay.payment');
});

// Route callback VNPAY về (GET), vẫn có session vì thuộc web.php, nhưng không yêu cầu login
Route::get('/vnpay_return', [AddGioHangController::class, 'vnpayReturn'])
     ->name('vnpay.return');


Route::get('layouts/timkiemSP', function () {
    return view('layouts/timkiemSP');
});

// Route::get('/dangnhap', function () {
//     return view('layouts.dangnhap');
// });
// Route::get('/dangky', function () {
//     return view('layouts.dangky');
// });
Route::get('/quenmatkhau', function () {
    return view('layouts.quenmatkhau');
});

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Quản lý sản phẩm
    Route::get('product', [AdminController::class, 'product'])->name('product.index');
    Route::get('product/{id}/edit', [AdminController::class, 'productEdit'])->name('product.edit');
    Route::patch('product/{id}', [AdminController::class, 'productUpdate'])->name('product.update');
    Route::get('product/create', [AdminController::class, 'productCreate'])->name('product.create');
    Route::post('product', [AdminController::class, 'productStore'])->name('product.store');
    Route::delete('product/{id}', [AdminController::class, 'productDestroy'])->name('product.destroy');
    Route::get('product/import-price', [AdminController::class, 'getImportPrice'])->name('product.import-price');
    //Quản lý biển thể sản phẩm
    Route::get('variants/{variant}/edit', [AdminController::class, 'variantEdit'])->name('variant.edit');
    Route::patch('variants/{variant}', [AdminController::class, 'variantUpdate'])->name('variant.update');
    Route::delete('variants/{variant}', [AdminController::class, 'variantDestroy'])->name('variant.destroy');
    //Quản lý người dùng
    Route::get('users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('users.create');
    Route::post('/users', [AdminController::class, 'usersStore'])->name('users.store');
    Route::get('users/{id}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
    Route::patch('users/{id}', [AdminController::class, 'usersUpdate'])->name('users.update');
    Route::delete('users/{id}', [AdminController::class, 'usersDestroy'])->name('users.destroy');
    //Thống kê
    Route::get('report', [AdminController::class, 'reportrevenue'])->name('report.revenue');
    Route::get('revenue/export', [AdminController::class, 'exportRevenue'])->name('revenue.export');
    Route::get('revenue/print', [AdminController::class, 'printRevenue'])->name('revenue.print');
    //Danh Mục
    Route::get('categories', [AdminController::class, 'categoryIndex'])->name('categories.index');
    Route::get('categories/create', [AdminController::class, 'categoryCreate'])->name('categories.create');
    Route::post('categories', [AdminController::class, 'categoryStore'])->name('categories.store');
    Route::get('categories/{id}/edit', [AdminController::class, 'categoryEdit'])->name('categories.edit');
    Route::patch('categories/{id}', [AdminController::class, 'categoryUpdate'])->name('categories.update');
    Route::delete('categories/{id}', [AdminController::class, 'categoryDestroy'])->name('categories.destroy');
    //Đơn Hàng
    Route::get('orders', [AdminController::class, 'ordersIndex'])->name('orders.index');
    Route::get('orders/{order}', [AdminController::class, 'ordersShow'])->name('orders.show');
    Route::patch('orders/{order}/delivery-status', [AdminController::class, 'updateDeliveryStatus'])->name('orders.updateDeliveryStatus');
    Route::patch('orders/{order}/notes', [AdminController::class, 'ordersUpdateNotes'])->name('orders.updateNotes');
    Route::patch('orders/{order}/refund',[AdminController::class, 'ordersRefund'])->name('orders.refund');
    //Route::delete('orders/{order}', [AdminController::class, 'ordersDestroy'])->name('orders.destroy');
    //Members
    Route::get('/members', [AdminController::class, 'membersIndex'])->name('members.index');
    Route::get('/members/create', [AdminController::class, 'membersCreate'])->name('members.create');
    Route::post('/members', [AdminController::class, 'membersStore'])->name('members.store');
    Route::get('/members/{id}', [AdminController::class, 'membersShow'])->name('members.show');
    Route::get('/members/{id}/edit', [AdminController::class, 'membersEdit'])->name('members.edit');
    Route::put('/members/{id}', [AdminController::class, 'membersUpdate'])->name('members.update');
    Route::delete('/members/{id}', [AdminController::class, 'membersDestroy'])->name('members.destroy');
    //Quản lý kho
    Route::get('inventory', [AdminController::class, 'inventoryIndex'])->name('inventory.index');
    Route::get('inventory/last-import-price/{id}', [AdminController::class, 'lastImportPrice'])->name('inventory.lastImportPrice');
    // Feedback
    Route::get('feedback', [AdminController::class, 'feedbackIndex'])->name('feedback.index');
    Route::post('feedback/{id}/reply', [AdminController::class, 'feedbackReply'])->name('feedback.reply');
    Route::delete('feedback/{id}', [AdminController::class, 'feedbackDestroy'])->name('feedback.destroy');
    // Voucher
    Route::get('vouchers', [AdminController::class, 'vouchersIndex'])->name('vouchers.index');
    Route::get('vouchers/create', [AdminController::class, 'vouchersCreate'])->name('vouchers.create');
    Route::post('vouchers', [AdminController::class, 'vouchersStore'])->name('vouchers.store');
    Route::get('vouchers/{id}/edit', [AdminController::class, 'vouchersEdit'])->name('vouchers.edit');
    Route::put('vouchers/{voucher}', [AdminController::class, 'vouchersUpdate'])->name('vouchers.update');
    Route::delete('vouchers/{voucher}', [AdminController::class, 'vouchersDestroy'])->name('vouchers.destroy');
    //
    Route::get('account', [AdminController::class, 'accountIndex'])->name('account.index');
    Route::post('account', [AdminController::class, 'accountUpdate'])->name('account.update');
});

