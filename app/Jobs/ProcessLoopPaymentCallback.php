<?php

namespace App\Jobs;

use App\Events\PaymentStatusUpdated;
use App\Models\LoopPayment;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessLoopPaymentCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $payload,
        public array $headers = []
    ) {}

    public function handle(): void
    {
        $merchantRef   = data_get($this->payload, 'reference', data_get($this->payload, 'merchantReference', data_get($this->payload, 'merchant_reference', data_get($this->payload, 'data.response.responseDetails.extRefNo'))));
        $transactionId = data_get($this->payload, 'txnReference', data_get($this->payload, 'transactionId', data_get($this->payload, 'data.txnReference')));
        $receipt       = data_get($this->payload, 'data.response.responseDetails.loopRefNo', data_get($this->payload, 'receiptNumber', data_get($this->payload, 'receipt_number')));
        $status        = strtoupper((string) data_get($this->payload, 'data.serviceTransactionStatus', data_get($this->payload, 'status', data_get($this->payload, 'resultCode', ''))));
        $code          = (string) data_get($this->payload, 'statusCode', data_get($this->payload, 'data.response.responseDetails.rspCode', data_get($this->payload, 'responseCode', '00')));
        $message       = data_get($this->payload, 'message', data_get($this->payload, 'data.response.rspMessage', ''));

        Log::info('Processing LOOP Payment Callback Job', [
            'merchant_reference' => $merchantRef,
            'transaction_id'     => $transactionId,
            'status'             => $status,
        ]);

        $query = LoopPayment::query();

        if ($merchantRef) {
            $query->where('merchant_reference', $merchantRef);
        } elseif ($transactionId) {
            $query->where('provider_transaction_id', $transactionId);
        } else {
            Log::warning('LOOP webhook missing lookup reference', $this->payload);
            return;
        }

        $payment = $query->first();

        if (!$payment) {
            Log::error('LOOP payment record not found for webhook reference', ['reference' => $merchantRef ?: $transactionId]);
            return;
        }

        DB::transaction(function () use ($payment, $transactionId, $receipt, $status, $code, $message) {
            $lockedPayment = LoopPayment::whereKey($payment->id)->lockForUpdate()->first();

            if (!$lockedPayment) return;

            // Never downgrade terminal successful payment back to pending
            if ($lockedPayment->status === LoopPayment::STATUS_SUCCESSFUL && $status !== 'REVERSED') {
                Log::info('LOOP Payment already marked successful, skipping duplicate callback processing.', ['id' => $lockedPayment->id]);
                return;
            }

            $isSuccess = in_array($status, ['SUCCESS', 'SUCCESSFUL', 'COMPLETED', 'PAID', '00', '0'], true) || $code === '00' || $code === '0';
            $isFailed  = in_array($status, ['FAILED', 'REJECTED', 'CANCELLED', 'DECLINED'], true);
            $isReverse = $status === 'REVERSED';

            $newStatus = $lockedPayment->status;
            if ($isSuccess) {
                $newStatus = LoopPayment::STATUS_SUCCESSFUL;
            } elseif ($isReverse) {
                $newStatus = LoopPayment::STATUS_REVERSED;
            } elseif ($isFailed) {
                $newStatus = LoopPayment::STATUS_FAILED;
            }

            $lockedPayment->update([
                'status'                  => $newStatus,
                'provider_transaction_id' => $transactionId ?: $lockedPayment->provider_transaction_id,
                'provider_receipt'        => $receipt ?: $lockedPayment->provider_receipt,
                'provider_code'           => $code,
                'provider_message'        => $message,
                'callback_snapshot'       => $this->payload,
                'completed_at'            => $isSuccess ? now() : $lockedPayment->completed_at,
                'failed_at'               => $isFailed ? now() : $lockedPayment->failed_at,
            ]);

            // Sync corresponding Order
            if ($lockedPayment->order_id) {
                $order = Order::whereKey($lockedPayment->order_id)->lockForUpdate()->first();
                if ($order && $isSuccess && $order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status'    => 'paid',
                        'order_status'      => 'accepted',
                        'paid_at'           => now(),
                        'payment_reference' => $receipt ?: ($transactionId ?: $lockedPayment->merchant_reference),
                    ]);

                    event(new PaymentStatusUpdated($order->fresh(), 'paid', 'accepted'));
                } elseif ($order && $isFailed && $order->payment_status === 'pending') {
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                }
            }
        });
    }
}
