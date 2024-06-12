<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', [\App\Http\Controllers\Public\IndexController::class, 'index'])->name('public.index');
Route::get('/{id}/detail', [\App\Http\Controllers\Public\IndexController::class, 'show'])->name('public.show');
Route::get('/order', [\App\Http\Controllers\Public\OrderController::class, 'index'])->name('order.index');

Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
