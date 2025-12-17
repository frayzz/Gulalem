<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('qty', 12, 3);
            $table->decimal('price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->timestamps();
        });

        Schema::create('cash_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('store_id')->nullable()->constrained('stores');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('cash_start', 12, 2);
            $table->decimal('cash_end', 12, 2)->nullable();
            $table->decimal('cash_diff', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->enum('method', ['cash', 'card', 'transfer', 'mixed']);
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->useCurrent();
            $table->foreignId('shift_id')->nullable()->constrained('cash_shifts');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_shifts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
    }
};
