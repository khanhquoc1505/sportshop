<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChiTietController;
use App\Models\SanPham;
use App\Models\BoMon;


Route::get('/', function () {
    return view('home/trangchu');
});

Route::get('/', function () {
    $products = SanPham::all();
    $bomons = BoMon::with('sanPhams')->get();
    return view('layouts.chinh', compact('products','bomons'));
})->name('layouts.chinh');

Route::get('chitiet/{id}', [ChiTietController::class, 'show'])->name('product.show');
Route::post('/cart/add/{product}', [ChiTietController::class, 'add'])->name('cart.add');
Route::middleware('auth')->group(function(){
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
    //Quản lý người dùng
    Route::get('users', [AdminController::class, 'users'])->name('users.index');
    Route::get('users/{id}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
    Route::patch('users/{id}', [AdminController::class, 'usersUpdate'])->name('users.update');
    Route::delete('users/{id}', [AdminController::class, 'usersDestroy'])->name('users.destroy');
    //Thống kê
    Route::get('report', [AdminController::class, 'reportrevenue'])->name('report.revenue');
    Route::get('revenue/export',[AdminController::class,'exportRevenue'])->name('revenue.export');
    Route::get('revenue/print',[AdminController::class,'printRevenue'])->name('revenue.print');
    //Danh Mục
    Route::get('categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::get('categories/create',[AdminController::class,'categoriesCreate'])->name('categories.create');
    Route::post('categories',[AdminController::class,'categoriesStore'])->name('categories.store');
    Route::get('categories/{id}/edit', [AdminController::class,'categoriesEdit'])->name('categories.edit');
    Route::patch ('categories/{id}',[AdminController::class,'categoriesUpdate'])->name('categories.update');
    Route::delete('categories/{id}', [AdminController::class, 'categoriesDestroy'])->name('categories.destroy');
    //Đơn Hàng
    Route::get('orders',[AdminController::class,'orders'])->name('orders.index');
    Route::get('orders/{id}', [AdminController::class,'ordersShow'])->name('orders.show');
    Route::delete('orders/{id}',[AdminController::class,'ordersDestroy'])->name('orders.destroy');
    Route::post('orders/{id}/notes', [AdminController::class, 'ordersUpdateNotes'])->name('orders.updateNotes');
    //Members
    Route::get('/members',[AdminController::class, 'members'])->name('members.index');
    Route::get('/members/create',  [AdminController::class, 'membersCreate'])->name('members.create');
    Route::post('/members',[AdminController::class, 'membersStore'])->name('members.store');
    Route::get('/members/{id}',[AdminController::class, 'membersShow'])->name('members.show');
    Route::get('/members/{id}/edit',[AdminController::class, 'membersEdit'])->name('members.edit');
    Route::put('/members/{id}',[AdminController::class, 'membersUpdate'])->name('members.update');
    Route::delete('/members/{id}',[AdminController::class, 'membersDestroy'])->name('members.destroy');
});

