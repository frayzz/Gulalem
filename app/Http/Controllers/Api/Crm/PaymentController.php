<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'method' => ['required', 'in:cash,card,transfer,mixed'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'shift_id' => ['nullable', 'exists:cash_shifts,id'],
        ]);

        $payment = DB::transaction(function () use ($order, $data) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $data['method'],
                'amount' => $data['amount'],
                'paid_at' => $data['paid_at'] ?? now(),
                'shift_id' => $data['shift_id'] ?? null,
            ]);

            $order->increment('paid_total', $data['amount']);
            $order->refresh();
            $order->update([
                'payment_status' => $order->paid_total >= $order->total ? 'paid' : 'partial',
            ]);

            return $payment;
        });

        return response()->json($payment, 201);
    }
}
