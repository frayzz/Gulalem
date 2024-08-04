<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Public\IndexController;
use App\Http\Controllers\Public\OrderController;



Route::get('/', [IndexController::class, 'index'])->name('public.index');
Route::get('/product/{slug}', [IndexController::class, 'show'])->name('public.show');
// Order
Route::get('/{slug}/order', [OrderController::class, 'index'])->name('order.index');
Route::post('/submit-order', [OrderController::class, 'store'])->name('submit.order');

// SMS Mobizon Verif
Route::post('/send-verification-code', [OrderController::class, 'sendVerificationCode'])->name('send.verification.code');
Route::post('/verify-code', [OrderController::class, 'verifyCode'])->name('verify.code');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/next-stage', [App\Http\Controllers\Admin\OrderController::class, 'moveToNextStage'])->name('orders.nextStage');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
