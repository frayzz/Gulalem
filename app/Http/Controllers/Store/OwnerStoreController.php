<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OwnerStoreController extends Controller
{
    public function edit()
    {
        // предполагаем, что у User есть связь store()
        $store = auth()->user()->store;

        abort_if(!$store, 404);

        return view('pages.store.owner.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $store = auth()->user()->store;
        abort_if(!$store, 404);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'slug'         => 'required|string|max:255|unique:stores,slug,' . $store->id,
            'description'  => 'nullable|string|max:1000',
            'city'         => 'nullable|string|max:255',
            'region'       => 'nullable|string|max:255',
            'street'       => 'nullable|string|max:255',
            'postal_code'  => 'nullable|string|max:20',
            'phone'        => 'nullable|string|max:32',
            'logo'         => 'nullable|image|max:4096',
            'order_channel' => 'required|in:form,whatsapp',
        ]);

        // загрузка логотипа
        if ($request->hasFile('logo')) {
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }

            $validated['logo'] = $request->file('logo')->store('stores', 'public');
        }

        $store->update($validated);

        return back()->with('success', 'Данные магазина обновлены');
    }
}
