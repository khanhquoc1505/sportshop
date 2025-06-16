<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;


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
    Route::get('revenue/export', [AdminController::class, 'exportRevenue'])->name('revenue.export');
    Route::get('revenue/print', [AdminController::class, 'printRevenue'])->name('revenue.print');
    //Danh Mục
    Route::get('categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::get('categories/create', [AdminController::class, 'categoriesCreate'])->name('categories.create');
    Route::post('categories', [AdminController::class, 'categoriesStore'])->name('categories.store');
    Route::get('categories/{id}/edit', [AdminController::class, 'categoriesEdit'])->name('categories.edit');
    Route::patch('categories/{id}', [AdminController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('categories/{id}', [AdminController::class, 'categoriesDestroy'])->name('categories.destroy');
    //Đơn Hàng
    Route::get('orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::get('orders/{id}', [AdminController::class, 'ordersShow'])->name('orders.show');
    Route::delete('orders/{id}', [AdminController::class, 'ordersDestroy'])->name('orders.destroy');
    Route::post('orders/{id}/notes', [AdminController::class, 'ordersUpdateNotes'])->name('orders.updateNotes');
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
    // Feedback
    Route::get('feedback', [AdminController::class, 'feedbackIndex'])->name('feedback.index');
    Route::post('feedback/{id}/reply', [AdminController::class, 'feedbackReply'])->name('feedback.reply');
    Route::delete('feedback/{id}', [AdminController::class, 'feedbackDestroy'])->name('feedback.destroy');
    // Voucher
    Route::get('vouchers', [AdminController::class, 'vouchersIndex'])->name('vouchers.index');
    Route::get('vouchers/create', [AdminController::class, 'vouchersCreate'])->name('vouchers.create');
    Route::post('vouchers', [AdminController::class, 'vouchersStore'])->name('vouchers.store');
    Route::get('vouchers/{code}/edit', [AdminController::class, 'vouchersEdit'])->name('vouchers.edit');
    Route::put('vouchers/{code}', [AdminController::class, 'vouchersUpdate'])->name('vouchers.update');
    Route::delete('vouchers/{code}', [AdminController::class, 'vouchersDestroy'])->name('vouchers.destroy');
    //
    Route::get('account', [AdminController::class, 'accountIndex'])->name('account.index');
    Route::post('account', [AdminController::class, 'accountUpdate'])->name('account.update');
});