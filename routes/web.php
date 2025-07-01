<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChiTietController;
use App\Models\SanPham;
use App\Models\BoMon;

Route::get('/', function () {
    $products = SanPham::all();
    $bomons = BoMon::with('sanPhams')->get();
    return view('layouts.chinh', compact('products', 'bomons'));
})->name('layouts.chinh');

Route::get('chitiet/{id}', [ChiTietController::class, 'show'])->name('product.show');
Route::post('/cart/add/{product}', [ChiTietController::class, 'add'])->name('cart.add');
Route::middleware('auth')->group(function () {
    Route::post('/wishlist/toggle/{product}', [ChiTietController::class, 'toggle'])->name('wishlist.toggle');
});
Route::get('product/{id}/mo-ta', [ChiTietController::class, 'moTa'])
    ->name('product.mo_ta');
////////////////////////////////////////////////////////////////////////
Route::get('/dangky', [HomeController::class, 'showRegisterForm'])->name('dangky.form');
Route::post('/dangky', [HomeController::class, 'dangky'])->name('dangky');

Route::get('/dangnhap', [HomeController::class, 'showLoginForm'])->name('login');
Route::post('/dangnhap', [HomeController::class, 'login'])->name('login');

Route::post('/dang-xuat', [HomeController::class, 'logout'])->name('logout');
////////////////////////////////////////////////////////////////////////
Route::get('layouts/timkiemSP', function () {
    return view('layouts/timkiemSP');
});

Route::get('/dangnhap', function () {
    return view('layouts.dangnhap');
});
Route::get('/dangky', function () {
    return view('layouts.dangky');
});
Route::get('/quenmatkhau', function () {
    return view('layouts.quenmatkhau');
});
Route::get('/giohang', function () {
    return view('layouts.giohang');
});
Route::get('/donhang', function () {
    return view('layouts.donhang');
});
Route::get('/chitietdonhang', function () {
    return view('layouts.chitietdonhang');
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
    Route::delete('orders/{order}', [AdminController::class, 'ordersDestroy'])->name('orders.destroy');
    Route::patch('orders/{order}/notes', [AdminController::class, 'ordersUpdateNotes'])->name('orders.updateNotes');
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

