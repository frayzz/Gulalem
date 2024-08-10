<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Category;

class SitemapController extends Controller
{
    public function generate()
    {
        $sitemap = Sitemap::create();

        // Добавьте URL главной страницы
        $sitemap->add(Url::create('/'));

        // Добавьте URL для страницы со списком всех категорий
        $sitemap->add(Url::create('/categories'));

        // Добавьте URL для всех категорий
        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap->add(Url::create("/categories/{$category->slug}"));
        }

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

