<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index($id) {
        $product = Product::find($id);
        return view('pages.order', compact('product'));
    }
}
