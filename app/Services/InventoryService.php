<?php

namespace App\Services;

use App\Models\BouquetRecipeItem;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\ProductBatch;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function recordInbound(array $data): ProductBatch
    {
        return DB::transaction(function () use ($data) {
            $batch = ProductBatch::create([
                'product_id' => $data['product_id'],
                'supplier_id' => $data['supplier_id'] ?? null,
                'store_id' => $data['store_id'] ?? null,
                'buy_price' => $data['buy_price'],
                'qty_in' => $data['qty'],
                'qty_left' => $data['qty'],
                'arrived_at' => Carbon::parse($data['arrived_at'] ?? now())->toDateString(),
                'expires_at' => isset($data['expires_at']) ? Carbon::parse($data['expires_at'])->toDateString() : null,
            ]);

            InventoryMovement::create([
                'product_id' => $batch->product_id,
                'batch_id' => $batch->id,
                'type' => 'in',
                'qty' => $batch->qty_in,
                'reason' => $data['reason'] ?? 'purchase',
                'user_id' => $data['user_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            return $batch;
        });
    }

    public function recordAdjustment(array $data): InventoryMovement
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['batch_id'])) {
                ProductBatch::findOrFail($data['batch_id']);
            }

            return InventoryMovement::create([
                'product_id' => $data['product_id'],
                'batch_id' => $data['batch_id'] ?? null,
                'type' => $data['type'] ?? 'adjust',
                'qty' => $data['qty'],
                'reason' => $data['reason'] ?? 'manual',
                'user_id' => $data['user_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]);
        });
    }

    public function consumeProduct(int $productId, float $qty, ?int $orderId = null, ?int $userId = null, string $reason = 'order'): void
    {
        DB::transaction(function () use ($productId, $qty, $orderId, $userId, $reason) {
            $remaining = $qty;

            $batches = ProductBatch::where('product_id', $productId)
                ->where('qty_left', '>', 0)
                ->orderBy('arrived_at')
                ->lockForUpdate()
                ->get();

            /** @var ProductBatch $batch */
            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $deduct = min($batch->qty_left, $remaining);
                $batch->qty_left = $batch->qty_left - $deduct;
                $batch->save();

                InventoryMovement::create([
                    'product_id' => $productId,
                    'batch_id' => $batch->id,
                    'type' => 'out',
                    'qty' => $deduct,
                    'reason' => $reason,
                    'order_id' => $orderId,
                    'user_id' => $userId,
                ]);

                $remaining -= $deduct;
            }

            if ($remaining > 0) {
                Log::warning('Inventory consumption exceeded available qty', [
                    'product_id' => $productId,
                    'requested' => $qty,
                    'missing' => $remaining,
                ]);
            }
        });
    }

    public function consumeForOrder(Order $order): void
    {
        $order->loadMissing(['items.product.bouquetRecipe.items']);

        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            if ($product->type === 'bouquet' && $product->bouquetRecipe) {
                $this->consumeBouquetRecipe($product->bouquetRecipe->items, $item->qty, $order->id, $order->user_id);
            } else {
                $this->consumeProduct($product->id, $item->qty, $order->id, $order->user_id, 'order');
            }
        }
    }

    protected function consumeBouquetRecipe(Collection $items, float $multiplier, ?int $orderId, ?int $userId): void
    {
        foreach ($items as $recipeItem) {
            /** @var BouquetRecipeItem $recipeItem */
            $this->consumeProduct(
                $recipeItem->product_id,
                $recipeItem->qty * $multiplier,
                $orderId,
                $userId,
                'bouquet'
            );
        }
    }
}
