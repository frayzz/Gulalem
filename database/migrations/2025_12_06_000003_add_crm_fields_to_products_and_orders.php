<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'type')) {
                $table->string('type')->default('flower');
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->default('pcs');
            }
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique();
            }
            if (!Schema::hasColumn('products', 'price_min')) {
                $table->decimal('price_min', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable();
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers');
            }
            if (!Schema::hasColumn('orders', 'delivery_type')) {
                $table->string('delivery_type')->nullable();
            }
            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->string('delivery_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'delivery_time')) {
                $table->timestamp('delivery_time')->nullable();
            }
            if (!Schema::hasColumn('orders', 'total')) {
                $table->decimal('total', 14, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'paid_total')) {
                $table->decimal('paid_total', 14, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('unpaid');
            }
            if (!Schema::hasColumn('orders', 'store_id')) {
                $table->foreignId('store_id')->nullable()->constrained('stores');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'store_id')) {
                $table->dropConstrainedForeignId('store_id');
            }
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('orders', 'paid_total')) {
                $table->dropColumn('paid_total');
            }
            if (Schema::hasColumn('orders', 'discount')) {
                $table->dropColumn('discount');
            }
            if (Schema::hasColumn('orders', 'total')) {
                $table->dropColumn('total');
            }
            if (Schema::hasColumn('orders', 'delivery_time')) {
                $table->dropColumn('delivery_time');
            }
            if (Schema::hasColumn('orders', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }
            if (Schema::hasColumn('orders', 'delivery_type')) {
                $table->dropColumn('delivery_type');
            }
            if (Schema::hasColumn('orders', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
            if (Schema::hasColumn('products', 'price_min')) {
                $table->dropColumn('price_min');
            }
            if (Schema::hasColumn('products', 'sku')) {
                $table->dropUnique('products_sku_unique');
                $table->dropColumn('sku');
            }
            if (Schema::hasColumn('products', 'unit')) {
                $table->dropColumn('unit');
            }
            if (Schema::hasColumn('products', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
