<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(Request $request) {
        // Получаем значения фильтров из запроса
        $sort = $request->input('sort');
        $price = $request->input('price');

        // Если пользователь авторизован
        if (Auth::check()) {
            $user = Auth::user(); // Получаем текущего авторизованного пользователя

            if ($user->orders()->exists()) {
                $activeOrders = $user->orders()->select('id', 'status', 'order_details')->get();
            }
        }

        // Базовый запрос
        $query = Product::query();

        // Обработка сортировки
        if ($sort) {
            switch ($sort) {
                case 'popular':
                    $query->orderBy('popularity', 'desc');
                    break;
                case 'new':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'price_low_high':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high_low':
                    $query->orderBy('price', 'desc');
                    break;
            }
        }

        // Обработка фильтрации по цене
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

        // Получаем отфильтрованные данные
        $products = $query->get();

        if ( isset($activeOrders) ) {  
            return view('pages.home', compact('products', 'activeOrders'));
        } else {
            return view('pages.home', compact('products'));
        }
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
