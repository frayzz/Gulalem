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
        // Шаг 1: Создать таблицу categories, если она не существует
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });

            // Вставить категорию по умолчанию непосредственно через SQL
            DB::table('categories')->insert([
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Шаг 2: Получить ID категории по умолчанию
        $defaultCategoryId = DB::table('categories')->where('slug', 'uncategorized')->value('id');

        // Шаг 3: Добавить столбец category_id в таблицу products, если он ещё не существует
        if (!Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->constrained('categories');
            });

            // Обновить существующие продукты, установив им категорию по умолчанию
            DB::table('products')->whereNull('category_id')->update(['category_id' => $defaultCategoryId]);

            // Сделать category_id обязательным
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удалить внешний ключ и столбец category_id из таблицы products
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        // Удалить таблицу categories, если необходимо
        Schema::dropIfExists('categories');
    }
};
