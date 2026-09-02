<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LoopPayment;
use App\Models\Order;
use App\Services\Loop\InitiateLoopPaybillPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoopPaybillController extends Controller
{
    /**
     * POST /api/loop/paybill-payments
     * Direct creation and initiation of a LOOP Paybill payment.
     */
    public function store(Request $request, InitiateLoopPaybillPayment $initiator): JsonResponse
    {
        $data = $request->validate([
            'order_id'          => ['required', 'integer', 'exists:orders,id'],
            'paybill_number'    => ['nullable', 'string', 'regex:/^[0-9]+$/', 'max:20'],
            'account_reference' => ['nullable', 'string', 'max:100'],
            'amount'            => ['nullable', 'numeric', 'gt:0'],
            'currency'          => ['nullable', 'string', 'size:3'],
            'narration'         => ['nullable', 'string', 'max:140'],
        ]);

        $order = Order::findOrFail($data['order_id']);
        $this->authorize('pay', $order);

        $payment = $initiator->handle($order, $data, $request->user());

        return response()->json([
            'status'     => 'success',
            'message'    => 'LOOP Paybill payment initiated.',
            'payment'    => [
                'public_id'          => $payment->public_id,
                'merchant_reference' => $payment->merchant_reference,
                'status'             => $payment->status,
                'amount'             => $payment->amount,
                'currency'           => $payment->currency,
                'paybill_number'     => $payment->paybill_number,
                'account_reference'  => $payment->account_reference,
            ],
            'order_id'   => $order->id,
        ], 202);
    }

    /**
     * GET /api/loop/paybill-payments/{payment:public_id}
     * Check status of a specific LOOP payment by public UUID.
     */
    public function show(Request $request, LoopPayment $payment): JsonResponse
    {
        if ($payment->order_id) {
            $order = Order::find($payment->order_id);
            if ($order) {
                $this->authorize('view', $order);
            }
        }

        return response()->json([
            'status'     => 'success',
            'payment'    => [
                'public_id'          => $payment->public_id,
                'merchant_reference' => $payment->merchant_reference,
                'status'             => $payment->status,
                'amount'             => $payment->amount,
                'provider_code'      => $payment->provider_code,
                'provider_message'   => $payment->provider_message,
                'completed_at'       => $payment->completed_at,
            ],
            'order_status'   => $payment->order?->order_status,
            'payment_status' => $payment->order?->payment_status,
        ]);
    }
}
