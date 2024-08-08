<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Добавьте URL главной страницы
        $sitemap->add(Url::create('/'));

        // Добавьте URL для всех продуктов
        $products = Product::all();
        foreach ($products as $product) {
            $sitemap->add(Url::create("/products/{$product->slug}"));
        }

        // Сохраните sitemap в файл
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully.');
    }
}
