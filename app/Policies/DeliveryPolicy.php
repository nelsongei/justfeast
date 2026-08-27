<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    /**
     * Determine whether the user can view the delivery details.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'runner') {
            return $delivery->runner_id === $user->id;
        }

        if ($user->role === 'vendor') {
            return $delivery->order && $user->vendor && $delivery->order->vendor_id === $user->vendor->id;
        }

        if ($user->role === 'customer') {
            return $delivery->order && $delivery->order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the runner can update the delivery status/location/verification.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'runner' && $delivery->runner_id === $user->id;
    }
}
