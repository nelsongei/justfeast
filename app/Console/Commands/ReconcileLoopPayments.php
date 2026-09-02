<?php

namespace App\Console\Commands;

use App\Events\PaymentStatusUpdated;
use App\Models\LoopPayment;
use App\Models\Order;
use App\Services\Loop\LoopClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReconcileLoopPayments extends Command
{
    protected $signature = 'loop:reconcile-payments';
    protected $description = 'Poll LOOP transaction inquiry API for pending or unknown transactions and update order states.';

    public function handle(LoopClient $loop): int
    {
        $this->info('Starting LOOP Paybill transaction reconciliation...');

        $payments = LoopPayment::query()
            ->whereIn('status', [LoopPayment::STATUS_PENDING, LoopPayment::STATUS_SUBMITTING, LoopPayment::STATUS_UNKNOWN])
            ->where('inquiry_attempts', '<', 10)
            ->where(function ($q) {
                $q->whereNull('last_inquired_at')
                  ->orWhere('last_inquired_at', '<=', now()->subMinutes(2));
            })
            ->limit(50)
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No pending or unknown LOOP payments required inquiry.');
            return 0;
        }

        $reconciled = 0;

        foreach ($payments as $payment) {
            $payment->increment('inquiry_attempts');
            $payment->update(['last_inquired_at' => now()]);

            $result = $loop->inquireTransaction(
                transactionId: $payment->provider_transaction_id ?? '',
                merchantReference: $payment->merchant_reference
            );

            $status = strtoupper((string) data_get($result, 'status', data_get($result, 'transactionStatus', '')));
            $receipt = data_get($result, 'receiptNumber', data_get($result, 'receipt_number'));

            if (empty($status) || $status === 'UNKNOWN') {
                continue;
            }

            DB::transaction(function () use ($payment, $status, $receipt, $result) {
                $locked = LoopPayment::whereKey($payment->id)->lockForUpdate()->first();
                if (!$locked) return;

                $isSuccess = in_array($status, ['SUCCESS', 'SUCCESSFUL', 'COMPLETED', 'PAID', '00', '0'], true);
                $isFailed  = in_array($status, ['FAILED', 'REJECTED', 'CANCELLED', 'DECLINED'], true);

                if ($isSuccess) {
                    $locked->update([
                        'status'            => LoopPayment::STATUS_SUCCESSFUL,
                        'provider_receipt'  => $receipt ?: $locked->provider_receipt,
                        'completed_at'      => now(),
                        'response_snapshot' => array_merge($locked->response_snapshot ?? [], ['inquiry' => $result]),
                    ]);

                    if ($locked->order_id) {
                        $order = Order::whereKey($locked->order_id)->lockForUpdate()->first();
                        if ($order && $order->payment_status !== 'paid') {
                            $order->update([
                                'payment_status'    => 'paid',
                                'order_status'      => 'accepted',
                                'paid_at'           => now(),
                                'payment_reference' => $receipt ?: ($locked->provider_transaction_id ?: $locked->merchant_reference),
                            ]);

                            event(new PaymentStatusUpdated($order->fresh(), 'paid', 'accepted'));
                        }
                    }
                } elseif ($isFailed) {
                    $locked->update([
                        'status'            => LoopPayment::STATUS_FAILED,
                        'failed_at'         => now(),
                        'response_snapshot' => array_merge($locked->response_snapshot ?? [], ['inquiry' => $result]),
                    ]);

                    if ($locked->order_id) {
                        $order = Order::whereKey($locked->order_id)->lockForUpdate()->first();
                        if ($order && $order->payment_status === 'pending') {
                            $order->update(['payment_status' => 'failed']);
                        }
                    }
                }
            });

            $reconciled++;
        }

        $this->info("Reconciled {$reconciled} LOOP payment(s).");
        return 0;
    }
}
