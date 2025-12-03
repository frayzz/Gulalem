<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Store\OwnerProductController;
use App\Http\Controllers\Store\OwnerStoreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Public\IndexController;
use App\Http\Controllers\Public\OrderController;
use App\Http\Controllers\Public\StoreController;



Route::get('/', [IndexController::class, 'index'])->name('public.index');
Route::get('/product/{slug}', [IndexController::class, 'show'])->name('public.show');

// Categories
Route::get('/categories', [App\Http\Controllers\Public\CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [App\Http\Controllers\Public\CategoryController::class, 'show'])->name('categories.show');
// Order
Route::get('/order/{slug}/', [OrderController::class, 'index'])->name('order.index');
Route::post('/submit-order', [OrderController::class, 'store'])->name('submit.order');
// Store
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('store.show');

// SMS Mobizon Verif
Route::post('/send-verification-code', [OrderController::class, 'sendVerificationCode'])->name('send.verification.code');
Route::post('/verify-code', [OrderController::class, 'verifyCode'])->name('verify.code');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/next-stage', [App\Http\Controllers\Admin\OrderController::class, 'moveToNextStage'])->name('orders.nextStage');

    Route::get('/generate-sitemap', [SitemapController::class, 'generate'])->name('generate-sitemap');

    Route::resource('categories', CategoryController::class)->except(['show']);
});

// Админка продавца
Route::middleware(['auth'])
    ->prefix('dashboard/store')
    ->name('store.')
    ->group(function () {
        // Редактирование данных магазина
        Route::get('/', [OwnerStoreController::class, 'edit'])->name('edit');
        Route::put('/', [OwnerStoreController::class, 'update'])->name('update');

        // Управление товарами
        Route::get('products', [OwnerProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [OwnerProductController::class, 'create'])->name('products.create');
        Route::post('products', [OwnerProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [OwnerProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [OwnerProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [OwnerProductController::class, 'destroy'])->name('products.destroy');
    });



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




require __DIR__.'/auth.php';
