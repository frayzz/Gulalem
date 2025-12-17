<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->text('notes')->nullable();
            $table->date('birthday')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('contact')->nullable();
            $table->timestamps();
        });

        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('store_id')->nullable()->constrained('stores');
            $table->decimal('buy_price', 10, 2);
            $table->decimal('qty_in', 12, 3);
            $table->decimal('qty_left', 12, 3);
            $table->date('arrived_at');
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('batch_id')->nullable()->constrained('product_batches');
            $table->enum('type', ['in', 'out', 'adjust', 'waste']);
            $table->decimal('qty', 12, 3);
            $table->string('reason')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('bouquet_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bouquet_product_id')->constrained('products');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('bouquet_recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('bouquet_recipes');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('qty', 12, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bouquet_recipe_items');
        Schema::dropIfExists('bouquet_recipes');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('product_batches');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
    }
};
