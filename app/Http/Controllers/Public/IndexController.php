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
        return view('pages.detail', compact('product'));
    }
}
