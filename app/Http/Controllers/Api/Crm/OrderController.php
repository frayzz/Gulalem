<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('delivery_type')) {
            $query->where('delivery_type', $request->string('delivery_type'));
        }

        return $query->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer.name' => ['required_without:customer_id', 'string', 'max:255'],
            'customer.phone' => ['required_without:customer_id', 'string', 'max:50'],
            'customer.notes' => ['nullable', 'string'],
            'delivery_type' => ['required', 'in:delivery,pickup,instore'],
            'delivery_address' => ['nullable', 'string'],
            'delivery_time' => ['nullable', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.001'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order = DB::transaction(function () use ($data, $request) {
            $customerId = $data['customer_id'] ?? null;
            $customerPhone = $data['customer']['phone'] ?? null;

            if (!$customerId && isset($data['customer'])) {
                $customer = Customer::create([
                    'name' => $data['customer']['name'],
                    'phone' => $data['customer']['phone'],
                    'notes' => $data['customer']['notes'] ?? null,
                    'created_by' => $request->user()->id ?? null,
                ]);
                $customerId = $customer->id;
                $customerPhone = $customer->phone;
            }

            if ($customerId && !$customerPhone) {
                $customerPhone = Customer::find($customerId)?->phone;
            }

            $totals = $this->calculateTotals($data['items'], $data['discount'] ?? 0);

            $order = Order::create([
                'customer_id' => $customerId,
                'delivery_type' => $data['delivery_type'],
                'delivery_address' => $data['delivery_address'] ?? null,
                'delivery_time' => $data['delivery_time'] ?? null,
                'discount' => $data['discount'] ?? 0,
                'total' => $totals['subtotal'],
                'paid_total' => 0,
                'payment_status' => 'unpaid',
                'status' => 'new',
                'payment_method' => $data['payment_method'] ?? null,
                'user_phone' => $customerPhone,
            ]);

            foreach ($data['items'] as $item) {
                $lineTotal = ($item['price'] * $item['qty']) - ($item['discount'] ?? 0);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'total' => $lineTotal,
                ]);
            }

            return $order->load(['customer', 'items.product']);
        });

        return response()->json($order, 201);
    }

    public function show(Order $order)
    {
        return $order->load(['customer', 'items.product', 'payments']);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'delivery_type' => ['sometimes', 'in:delivery,pickup,instore'],
            'delivery_address' => ['nullable', 'string'],
            'delivery_time' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
        ]);

        $order->update($data);

        return $order->fresh()->load(['customer', 'items.product']);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $order->update(['status' => $data['status']]);

        return $order->fresh();
    }

    public function complete(Order $order)
    {
        $order->update(['status' => 'completed']);
        $this->inventoryService->consumeForOrder($order);

        return $order->fresh()->load(['items.product']);
    }

    public function cancel(Order $order)
    {
        $order->update(['status' => 'cancelled']);

        return $order->fresh();
    }

    private function calculateTotals(array $items, float $orderDiscount = 0): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $lineTotal = ($item['price'] * $item['qty']) - ($item['discount'] ?? 0);
            $subtotal += $lineTotal;
        }

        return [
            'subtotal' => max($subtotal - $orderDiscount, 0),
        ];
    }
}
