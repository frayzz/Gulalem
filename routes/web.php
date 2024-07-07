<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Public\IndexController;
use App\Http\Controllers\Public\OrderController;



Route::get('/', [IndexController::class, 'index'])->name('public.index');
Route::get('/{id}/detail', [IndexController::class, 'show'])->name('public.show');
Route::get('/{id}/order', [OrderController::class, 'index'])->name('order.index');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
