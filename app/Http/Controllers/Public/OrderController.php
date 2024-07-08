<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index($id) {
        $product = Product::find($id);
        return view('pages.order', compact('product'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_phone' => 'required|string',
            'recipient_phone' => 'nullable|string',
            'address' => 'required|string',
            'selected_time' => 'required|string',
            'custom_time' => 'nullable|date',
            'card_message' => 'nullable|string',
            'payment_method' => 'required|string',
        ]);

        $order = new Order();
        $order->user_phone = $request->user_phone;
        $order->recipient_phone = $request->recipient_phone;
        $order->address = $request->address;
        $order->selected_time = $request->selected_time;
        $order->custom_time = $request->custom_time;
        $order->card_message = $request->card_message;
        $order->payment_method = $request->payment_method;
        $order->status = 'not-pay'; // Начальный статус
        $order->save();

        return redirect()->back()->with('success', 'Заказ успешно оформлен!');
    }
}
