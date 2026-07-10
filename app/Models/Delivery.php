<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'runner_id',
        'pickup_time',
        'delivered_time',
        'verification_pin',
        'status',
        'runner_latitude',
        'runner_longitude',
        'arrived_at',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'delivered_time' => 'datetime',
        'arrived_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function runner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'runner_id');
    }
}
