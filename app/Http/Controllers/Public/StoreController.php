<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function show(Request $request, $slug)
    {
        // фильтры из запроса
        $sort  = $request->input('sort');   // price_asc | price_desc | null
        $price = $request->input('price');  // 15000 / 20000 / 40000 / 60000 / null

        // Сначала загружаем сам магазин
        $store = Store::where('slug', $slug)->firstOrFail();

        // Базовый запрос: только активные товары с картинками
        $query = $store->products()
            ->where('is_active', true)
            ->with('images');

        // Фильтр по цене
        if ($price) {
            switch ($price) {
                case '15000':
                    $query->where('price', '<=', 15000);
                    break;
                case '20000':
                    $query->where('price', '<=', 20000);
                    break;
                case '40000':
                    $query->where('price', '<=', 40000);
                    break;
                case '60000':
                    $query->where('price', '<=', 60000);
                    break;
            }
        }

        // Сортировка по цене
        if ($sort) {
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
            }
        }

        // Получаем коллекцию товаров (можно потом заменить на paginate)
        $products = $query->get();

        $meta = [
            'title'          => $store->name,
            'description'    => $store->seo_description
                ?? ('Магазин ' . $store->name),
            'og_title'       => $store->name,
            'og_description' => $store->seo_description
                ?? ('Магазин ' . $store->name),
            'og_image'       => $store->logo
                ? asset('storage/' . $store->logo)
                : asset('images/no-image.png'),
        ];

        return view('pages.store', compact('store', 'products', 'meta', 'sort', 'price'));
    }
}
