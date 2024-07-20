<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductProperty;
use App\Models\Store;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    protected $stages = ['not-pay', 'awaiting-packing', 'packing', 'delivery', 'end'];
    public function index()
    {
        // Не завершенные заказы
        // По возрастанию
        $orders = Order::where('status', '!=', 'end')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function moveToNextStage($id)
    {
        // Найти заказ по ID
        $order = Order::findOrFail($id);

        // Получить текущий индекс стадии
        $currentStageIndex = array_search($order->status, $this->stages);

        // Определить следующую стадию
        if ($currentStageIndex !== false && $currentStageIndex < count($this->stages) - 1) {
            $nextStage = $this->stages[$currentStageIndex + 1];
            $order->status = $nextStage;
            $order->save();

            return redirect()->back()->with('success', 'Статус заказа обновлен до: ' . __('status.'.$nextStage));
        }

        return redirect()->back()->with('error', 'Не удалось обновить статус заказа.');
    }


}
