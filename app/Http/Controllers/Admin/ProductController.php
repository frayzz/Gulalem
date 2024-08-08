<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductProperty;
use App\Models\Store;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $stores = Store::all();
        return view('admin.products.create', compact('stores'));
    }

    public function store(Request $request)
    {
        // Валидация входных данных
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'store_id' => 'required|exists:stores,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'properties' => 'array',
            'properties.*.name' => 'nullable',
            'properties.*.value' => 'nullable',
        ]);

        // Создание продукта
        $product = Product::create($request->only('name', 'description', 'price', 'store_id'));

        // Обработка и сохранение свойств продукта
        if ($request->has('properties')) {
            foreach ($request->properties as $property) {
                if (!empty($property['name']) && !empty($property['value'])) {
                    $product->properties()->create([
                        'property_name' => $property['name'],
                        'property_value' => $property['value'],
                    ]);
                }
            }
        }

        // Обработка и сохранение изображений
        if ($request->hasFile('images')) {
            $optimizerChain = OptimizerChainFactory::create();
            foreach ($request->file('images') as $image) {
                // Сохранение изображения
                $path = $image->store('products', 'public');

                // Оптимизация изображения
                $optimizerChain->optimize(storage_path('app/public/' . $path));

                $product->images()->create([
                    'image_url' => $path,
                    'format' => 'original',
                    'type' => $image->getClientOriginalExtension(),
                ]);
            }
        }

        // Перенаправление с сообщением об успехе
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $stores = Store::all();
        return view('admin.products.edit', compact('product', 'stores'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'store_id' => 'required|exists:stores,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'properties' => 'array',
            'properties.*.name' => 'required_with:properties.*.value',
            'properties.*.value' => 'required_with:properties.*.name',
        ]);

        $product->update($request->only('name', 'description', 'price', 'store_id'));

        $product->properties()->delete();
        if ($request->has('properties')) {
            foreach ($request->properties as $property) {
                $product->properties()->create([
                    'property_name' => $property['name'],
                    'property_value' => $property['value'],
                ]);
            }
        }

        if ($request->hasFile('images')) {
            $product->images()->delete();
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_url' => $path,
                    'format' => 'original',
                    'type' => $image->getClientOriginalExtension(),
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
