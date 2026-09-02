<?php

namespace App\Services\Loop;

use App\Models\LoopPayment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InitiateLoopPaybillPayment
{
    /**
     * Create or retrieve active pending LOOP Paybill payment intent for customer collection.
     */
    public function handle(Order $order, array $input, User $user): LoopPayment
    {
        $existing = LoopPayment::where('order_id', $order->id)
            ->whereIn('status', [LoopPayment::STATUS_PENDING, LoopPayment::STATUS_CLAIMED, LoopPayment::STATUS_VERIFYING])
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($order) {
            $paybillNumber    = config('loop.paybill_number', '600100');
            $accountReference = LoopPayment::generateAccountReference($order);
            $merchantReference = 'LMP-ORDER-' . $order->id . '-' . Str::upper(Str::random(6));

            $payment = LoopPayment::create([
                'public_id'          => (string) Str::uuid(),
                'order_id'           => $order->id,
                'merchant_reference' => $merchantReference,
                'idempotency_key'    => (string) Str::uuid(),
                'paybill_number'     => $paybillNumber,
                'account_reference'  => $accountReference,
                'amount'             => number_format((float) $order->total_amount, 2, '.', ''),
                'currency'           => config('loop.currency', 'KES'),
                'narration'          => 'Customer Paybill Payment for Order #' . $order->id,
                'status'             => LoopPayment::STATUS_PENDING,
                'initiated_at'       => now(),
                'expires_at'         => now()->addMinutes(config('loop.payment_expiry_minutes', 30)),
            ]);

            $order->update([
                'payment_method'  => 'loop_paybill',
                'loop_payment_id' => $payment->id,
            ]);

            return $payment;
        });
    }
}
