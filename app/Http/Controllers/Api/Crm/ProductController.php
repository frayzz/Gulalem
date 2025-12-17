<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::orderByDesc('created_at')->get(['id', 'name', 'store_id', 'type', 'unit', 'price']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'store_id' => ['required', 'exists:stores,id'],
            'type' => ['nullable', 'string', 'max:50'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $product = Product::create($data);

        return response()->json($product, 201);
    }
}
