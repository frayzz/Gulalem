<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Добавляем колонку product_id, разрешая NULL значения временно
            $table->unsignedBigInteger('product_id')->nullable()->after('id');
        });

        // Обновляем существующие строки, устанавливая значение по умолчанию для product_id, если необходимо
        DB::table('orders')->update(['product_id' => 1]); // Предполагаем, что продукт с id=1 существует

        Schema::table('orders', function (Blueprint $table) {
            // Меняем колонку на NOT NULL и добавляем внешний ключ
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['product_id']); // Удаляем внешний ключ
            $table->dropColumn('product_id'); // Удаляем колонку
        });
    }
};
