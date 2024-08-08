<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;

class SitemapController extends Controller
{
    public function generate()
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

        return redirect()->back()->with('success', 'Sitemap generated successfully.');
    }
}
