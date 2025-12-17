<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Controller;
use App\Models\BouquetRecipe;
use Illuminate\Http\Request;

class BouquetRecipeController extends Controller
{
    public function index()
    {
        return BouquetRecipe::with('items')->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bouquet_product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'items' => ['array'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'numeric', 'min:0.001'],
        ]);

        $recipe = BouquetRecipe::create([
            'bouquet_product_id' => $data['bouquet_product_id'],
            'name' => $data['name'],
        ]);

        $this->syncItems($recipe, $data['items'] ?? []);

        return response()->json($recipe->load('items'), 201);
    }

    public function show(BouquetRecipe $bouquetRecipe)
    {
        return $bouquetRecipe->load('items');
    }

    public function update(Request $request, BouquetRecipe $bouquetRecipe)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'items' => ['array'],
            'items.*.product_id' => ['required_with:items', 'exists:products,id'],
            'items.*.qty' => ['required_with:items', 'numeric', 'min:0.001'],
        ]);

        if (isset($data['name'])) {
            $bouquetRecipe->update(['name' => $data['name']]);
        }

        if (isset($data['items'])) {
            $this->syncItems($bouquetRecipe, $data['items']);
        }

        return $bouquetRecipe->load('items');
    }

    public function destroy(BouquetRecipe $bouquetRecipe)
    {
        $bouquetRecipe->items()->delete();
        $bouquetRecipe->delete();

        return response()->noContent();
    }

    private function syncItems(BouquetRecipe $recipe, array $items): void
    {
        $recipe->items()->delete();

        foreach ($items as $item) {
            $recipe->items()->create([
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
            ]);
        }
    }
}
