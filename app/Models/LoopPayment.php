<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LoopPayment extends Model
{
    use HasFactory;

    public const STATUS_CREATED       = 'created';
    public const STATUS_SUBMITTING    = 'submitting';
    public const STATUS_PENDING       = 'pending';
    public const STATUS_CLAIMED       = 'customer_claimed';
    public const STATUS_VERIFYING     = 'verifying';
    public const STATUS_MANUAL_REVIEW = 'manual_review';
    public const STATUS_SUCCESSFUL    = 'successful';
    public const STATUS_FAILED        = 'failed';
    public const STATUS_EXPIRED       = 'expired';
    public const STATUS_REVERSED      = 'reversed';
    public const STATUS_UNKNOWN       = 'unknown';

    protected $fillable = [
        'public_id',
        'order_id',
        'merchant_reference',
        'idempotency_key',
        'paybill_number',
        'account_reference',
        'submitted_receipt',
        'amount',
        'currency',
        'narration',
        'status',
        'provider_transaction_id',
        'provider_request_id',
        'provider_receipt',
        'provider_code',
        'provider_message',
        'inquiry_attempts',
        'initiated_at',
        'customer_claimed_at',
        'expires_at',
        'completed_at',
        'failed_at',
        'last_inquired_at',
        'request_snapshot',
        'response_snapshot',
        'callback_snapshot',
    ];

    protected $casts = [
        'amount'              => 'decimal:2',
        'inquiry_attempts'    => 'integer',
        'initiated_at'        => 'datetime',
        'customer_claimed_at' => 'datetime',
        'expires_at'          => 'datetime',
        'completed_at'        => 'datetime',
        'failed_at'           => 'datetime',
        'last_inquired_at'    => 'datetime',
        'request_snapshot'    => 'array',
        'response_snapshot'   => 'array',
        'callback_snapshot'   => 'array',
    ];

    public static function generateAccountReference(Order $order): string
    {
        return 'JF' . base_convert((string) $order->id, 10, 36) . Str::upper(Str::random(4));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
