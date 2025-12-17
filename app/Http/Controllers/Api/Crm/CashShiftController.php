<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Controller;
use App\Models\CashShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashShiftController extends Controller
{
    public function index()
    {
        return CashShift::with('user')->latest()->paginate();
    }

    public function open(Request $request)
    {
        $data = $request->validate([
            'store_id' => ['nullable', 'exists:stores,id'],
            'cash_start' => ['required', 'numeric'],
        ]);

        $shift = CashShift::create([
            'user_id' => $request->user()->id ?? null,
            'store_id' => $data['store_id'] ?? null,
            'opened_at' => now(),
            'cash_start' => $data['cash_start'],
        ]);

        return response()->json($shift, 201);
    }

    public function close(Request $request, CashShift $cashShift)
    {
        $data = $request->validate([
            'cash_end' => ['required', 'numeric'],
        ]);

        $shift = DB::transaction(function () use ($cashShift, $data) {
            $cashShift->update([
                'closed_at' => now(),
                'cash_end' => $data['cash_end'],
                'cash_diff' => $data['cash_end'] - $cashShift->cash_start,
            ]);

            return $cashShift->fresh();
        });

        return $shift;
    }
}
