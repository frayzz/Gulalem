<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        // Загрузить категории с подкатегориями
        $categories = Category::with('children')->get();

        // Вернуть представление с категориями
        return view('pages.categories.index', compact('categories'));
    }

    public function show($slug)
    {
        // Найти категорию по слагу
        $category = Category::where('slug', $slug)->with('products')->firstOrFail();

        // Загрузить товары, связанные с категорией
        $products = $category->products;

        return view('pages.categories.show', [
            'category' => $category,
            'products' => $products,
            'title' => $category->name . ' - купить с доставкой',
            'metaDescription' => $category->description,
        ]);
    }
}
