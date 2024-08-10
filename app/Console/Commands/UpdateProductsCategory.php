<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;

class UpdateProductsCategory extends Command
{
    // Имя и описание команды
    protected $signature = 'products:update-category';
    protected $description = 'Update category_id for all products';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Найдем или создадим категорию, которую будем присваивать
        $category = Category::firstOrCreate(
            ['name' => 'Default Category'],
            ['slug' => 'default-category']
        );

        // Обновим все записи в таблице products, установив category_id
        Product::whereNull('category_id')->update(['category_id' => $category->id]);

        $this->info('All products have been updated with the default category.');
    }
}

