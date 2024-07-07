<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index() {
        $products = Product::with('images')->get();

        return view('pages.home', compact('products'));
    }

    public function show($id) {
        $product = Product::find($id);
        $meta = [
            'title' => $product->name,
            'description' => $product->description,
            'og_title' => $product->name,
            'og_description' => $product->description,
            'og_image' => $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_url) : asset('images/no-image.png'),
        ];

        return view('pages.detail', compact('product', 'meta'));
    }
}
