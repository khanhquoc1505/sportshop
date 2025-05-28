<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;


Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Quản lý kho giờ dùng AdminController@warehouse
    Route::get('product', [AdminController::class, 'product'])->name('product.index');
    // trang sửa
    Route::get('product/{id}/edit', [AdminController::class, 'productEdit'])->name('product.edit');
    // xử lý update
    Route::patch('product/{id}', [AdminController::class, 'productUpdate'])->name('product.update');
    // Link tới form "Thêm sản phẩm"
    Route::get('product/create', [AdminController::class, 'productCreate'])->name('product.create');
    // Route xử lý thêm mới
    Route::post('product', [AdminController::class, 'productStore'])->name('product.store');
    // 1. Danh sách + search & filter
    Route::get('users', [AdminController::class, 'users'])->name('users.index');
    // 2. Form chỉnh sửa
    Route::get('users/{id}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
    // 3. Xử lý lưu update
    Route::patch('users/{id}', [AdminController::class, 'usersUpdate'])->name('users.update');
    // 4. Xử lý xóa
    Route::delete('users/{id}', [AdminController::class, 'usersDestroy'])->name('users.destroy');
    //
    Route::get('report', [AdminController::class, 'reportrevenue'])->name('report.revenue');
    // export Excel
    Route::get('revenue/export',[AdminController::class,'exportRevenue'])->name('revenue.export');
    // print báo cáo
    Route::get('revenue/print',[AdminController::class,'printRevenue'])->name('revenue.print');
    // Trang danh sách danh mục
    Route::get('categories', [AdminController::class, 'categories'])->name('categories.index');
    // form thêm danh mục
    Route::get('categories/create',[AdminController::class,'categoriesCreate'])->name('categories.create');
    // xử lý lưu danh mục mới
    Route::post('categories',[AdminController::class,'categoriesStore'])->name('categories.store');
    // Form chỉnh sửa
    Route::get('categories/{id}/edit', [AdminController::class,'categoriesEdit'])->name('categories.edit');
    // Xử lý cập nhật
    Route::patch ('categories/{id}',[AdminController::class,'categoriesUpdate'])->name('categories.update');
    // Xóa danh mục
    Route::delete('categories/{id}', [AdminController::class, 'categoriesDestroy'])->name('categories.destroy');
});