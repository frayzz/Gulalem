<?php
// app/Http/Controllers/Store/ProductController.php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ProductProperty;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OwnerProductController extends Controller
{
    // список товаров магазина
    public function index()
    {
        $store = auth()->user()->store; // подстрой под свою логику связи
        $products = Product::with(['inventory', 'images'])
            ->where('store_id', $store->id)
            ->latest()
            ->paginate(20);

        return view('pages.store.products.index', compact('products', 'store'));
    }

    // форма создания
    public function create()
    {
        $store = auth()->user()->store;

        return view('pages.store.products.form', [
            'store' => $store,
            'product' => new Product(),
            'mode' => 'create',
        ]);
    }

    // сохранение нового товара
    public function store(Request $request)
    {
        $store = auth()->user()->store;

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'properties.*.name'  => 'nullable|string|max:255',
            'properties.*.value' => 'nullable|string|max:255',
            'images.*'    => 'nullable|image|max:4096',
        ]);

        // создаём товар
        $product = Product::create([
            'store_id'    => $store->id,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
        ]);

        // наличие
        Inventory::create([
            'product_id' => $product->id,
            'quantity'   => $validated['quantity'],
        ]);

        // свойства (если пришли)
        if ($request->filled('properties')) {
            foreach ($request->input('properties') as $prop) {
                if (!empty($prop['name']) && !empty($prop['value'])) {
                    ProductProperty::create([
                        'product_id'     => $product->id,
                        'property_name'  => $prop['name'],
                        'property_value' => $prop['value'],
                    ]);
                }
            }
        }

        // изображения
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url'  => $path,
                    'format'     => 'large', // можешь потом сделать генерацию разных форматов
                    'type'       => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return redirect()
            ->route('pages.store.products.index')
            ->with('success', 'Товар успешно создан');
    }

    // форма редактирования
    public function edit(Product $product)
    {
        $store = auth()->user()->store;

        // защита: нельзя редактировать чужой товар
        abort_if($product->store_id !== $store->id, 403);

        $product->load(['inventory', 'properties', 'images']);

        return view('pages.store.products.form', [
            'store'   => $store,
            'product' => $product,
            'mode'    => 'edit',
        ]);
    }

    // обновление товара
    public function update(Request $request, Product $product)
    {
        $store = auth()->user()->store;
        abort_if($product->store_id !== $store->id, 403);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'properties.*.id'    => 'nullable|integer',
            'properties.*.name'  => 'nullable|string|max:255',
            'properties.*.value' => 'nullable|string|max:255',
            'images.*'    => 'nullable|image|max:4096',
        ]);

        $product->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
        ]);

        // наличие
        if ($product->inventory) {
            $product->inventory->update([
                'quantity' => $validated['quantity'],
            ]);
        } else {
            Inventory::create([
                'product_id' => $product->id,
                'quantity'   => $validated['quantity'],
            ]);
        }

        // свойства — проще всего: удалить старые и создать заново
        $product->properties()->delete();
        if ($request->filled('properties')) {
            foreach ($request->input('properties') as $prop) {
                if (!empty($prop['name']) && !empty($prop['value'])) {
                    ProductProperty::create([
                        'product_id'     => $product->id,
                        'property_name'  => $prop['name'],
                        'property_value' => $prop['value'],
                    ]);
                }
            }
        }

        // изображения — добавляем новые (удаление/замена можно сделать отдельной кнопкой)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url'  => $path,
                    'format'     => 'large',
                    'type'       => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return redirect()
            ->route('pages.store.products.index')
            ->with('success', 'Товар обновлён');
    }

    // удаление товара
    public function destroy(Product $product)
    {
        $store = auth()->user()->store;
        abort_if($product->store_id !== $store->id, 403);

        // при onDelete('cascade') всё, что связано, удалится само
        $product->delete();

        return redirect()
            ->route('pages.store.products.index')
            ->with('success', 'Товар удалён');
    }
}

