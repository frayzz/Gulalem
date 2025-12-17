<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\ProductBatch;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function batches()
    {
        return ProductBatch::with(['product', 'supplier'])->orderByDesc('arrived_at')->paginate();
    }

    public function movements()
    {
        return InventoryMovement::with(['product', 'batch'])
            ->latest()
            ->paginate();
    }

    public function inbound(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'qty' => ['required', 'numeric', 'min:0.001'],
            'arrived_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'reason' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $data['user_id'] = $request->user()->id ?? null;

        $batch = $this->inventoryService->recordInbound($data);

        return response()->json($batch->load(['product', 'supplier']), 201);
    }

    public function outbound(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'numeric', 'min:0.001'],
            'reason' => ['nullable', 'string'],
        ]);

        $this->inventoryService->consumeProduct(
            $data['product_id'],
            $data['qty'],
            $request->integer('order_id'),
            $request->user()->id ?? null,
            $data['reason'] ?? 'manual'
        );

        return response()->json(['status' => 'ok']);
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'batch_id' => ['nullable', 'exists:product_batches,id'],
            'qty' => ['required', 'numeric'],
            'type' => ['nullable', 'in:adjust,waste'],
            'reason' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $data['user_id'] = $request->user()->id ?? null;

        $movement = $this->inventoryService->recordAdjustment($data);

        return response()->json($movement, 201);
    }
}
