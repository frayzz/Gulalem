<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Store;

class StoreController extends Controller
{
    public function show($slug)
    {
        $store = Store::where('slug', $slug)
            ->with(['products.images'])
            ->firstOrFail();

        $products = $store->products;

        $meta = [
            'title'         => $store->name . ' - магазин на Gulalem',
            'description'   => $store->seo_description
                ?? ('Магазин ' . $store->name . ' на Gulalem. Свежие букеты и цветы.'),
            'og_title'      => $store->name,
            'og_description'=> $store->seo_description
                ?? ('Магазин ' . $store->name . ' на Gulalem.'),
            'og_image'      => $store->logo
                ? asset('storage/' . $store->logo)
                : asset('images/no-image.png'),
        ];

        return view('pages.store', compact('store', 'products', 'meta'));
    }
}
