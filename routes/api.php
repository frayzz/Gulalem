<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Crm\BouquetRecipeController;
use App\Http\Controllers\Api\Crm\CashShiftController;
use App\Http\Controllers\Api\Crm\CustomerController;
use App\Http\Controllers\Api\Crm\InventoryController;
use App\Http\Controllers\Api\Crm\OrderController;
use App\Http\Controllers\Api\Crm\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('crm')->group(function () {
    Route::apiResource('customers', CustomerController::class);

    Route::get('inventory/batches', [InventoryController::class, 'batches']);
    Route::get('inventory/movements', [InventoryController::class, 'movements']);
    Route::post('inventory/in', [InventoryController::class, 'inbound']);
    Route::post('inventory/out', [InventoryController::class, 'outbound']);
    Route::post('inventory/adjust', [InventoryController::class, 'adjust']);

    Route::apiResource('bouquet-recipes', BouquetRecipeController::class);

    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::put('orders/{order}', [OrderController::class, 'update']);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::post('orders/{order}/complete', [OrderController::class, 'complete']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);

    Route::post('orders/{order}/payments', [PaymentController::class, 'store']);

    Route::get('cash-shifts', [CashShiftController::class, 'index']);
    Route::post('cash-shifts/open', [CashShiftController::class, 'open']);
    Route::post('cash-shifts/{cashShift}/close', [CashShiftController::class, 'close']);
});
