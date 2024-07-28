<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateProductSlugs extends Command
{
    protected $signature = 'products:update-slugs';
    protected $description = 'Update slugs for existing products';

    public function handle()
    {
        // Получаем все продукты, у которых slug пуст или null
        $products = Product::whereNull('slug')->orWhere('slug', '')->get();

        foreach ($products as $product) {
            // Проверяем, если slug пуст или null
            if (empty($product->slug)) {
                $product->slug = null; // Обнуляем slug, чтобы пакет его пересоздал
                $product->save();
            }
        }

        $this->info('Slugs updated for all products.');
    }
}

