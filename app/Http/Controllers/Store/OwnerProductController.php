<?php

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
    public function index(Request $request)
    {
        $store = auth()->user()->store; // подстрой под свою логику связи

        $status = $request->input('status'); // all | active | inactive

        $query = Product::with(['inventory', 'images'])
            ->where('store_id', $store->id)
            ->latest();

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $products = $query->paginate(20)->withQueryString();

        return view('pages.store.products.index', compact('products', 'store', 'status'));
    }

    // форма создания
    public function create()
    {
        $store = auth()->user()->store;

        return view('pages.store.products.form', [
            'store'   => $store,
            'product' => new Product(),
            'mode'    => 'create',
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
            'is_active'   => 'nullable|boolean',
            'properties.*.name'  => 'nullable|string|max:255',
            'properties.*.value' => 'nullable|string|max:255',
            // ОДНО изображение
            'image'       => 'nullable|image|max:4096',
        ]);

        // создаём товар
        $product = Product::create([
            'store_id'    => $store->id,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'is_active'   => $request->boolean('is_active', true),
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

        // изображение — только одно
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_url'  => $path,
                'format'     => 'large',
                'type'       => $file->getClientOriginalExtension(),
            ]);
        }

        return redirect()
            ->route('store.products.index')
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
            'is_active'   => 'nullable|boolean',
            'properties.*.id'    => 'nullable|integer',
            'properties.*.name'  => 'nullable|string|max:255',
            'properties.*.value' => 'nullable|string|max:255',
            // ОДНО изображение
            'image'       => 'nullable|image|max:4096',
        ]);

        $product->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'is_active'   => $request->boolean('is_active', true),
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

        // изображение — если загружено новое, то:
        // 1) удаляем старое(ые) изображение(я) + файлы
        // 2) создаём новое
        if ($request->hasFile('image')) {
            // удалить файлы старых изображений
            foreach ($product->images as $img) {
                if ($img->image_url) {
                    Storage::disk('public')->delete($img->image_url);
                }
            }
            // удалить записи в БД
            $product->images()->delete();

            // сохранить новое
            $file = $request->file('image');
            $path = $file->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_url'  => $path,
                'format'     => 'large',
                'type'       => $file->getClientOriginalExtension(),
            ]);
        }

        return redirect()
            ->route('store.products.index')
            ->with('success', 'Товар обновлён');
    }

    // быстрый переключатель активности
    public function toggleStatus(Product $product)
    {
        $store = auth()->user()->store;
        abort_if($product->store_id !== $store->id, 403);

        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return redirect()
            ->back()
            ->with('success', $product->is_active ? 'Товар включён в витрину' : 'Товар скрыт с витрины');
    }

    // удаление товара
    public function destroy(Product $product)
    {
        $store = auth()->user()->store;
        abort_if($product->store_id !== $store->id, 403);

        // заодно удалим файл(ы) картинки
        foreach ($product->images as $img) {
            if ($img->image_url) {
                Storage::disk('public')->delete($img->image_url);
            }
        }
        $product->images()->delete();

        $product->delete();

        return redirect()
            ->route('store.products.index')
            ->with('success', 'Товар удалён');
    }
}
