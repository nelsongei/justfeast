<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'customer') {
            return $order->user_id === $user->id;
        }

        if ($user->role === 'vendor') {
            return $user->vendor && $order->vendor_id === $user->vendor->id;
        }

        if ($user->role === 'runner') {
            return $order->runner_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the order status.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'vendor') {
            return $user->vendor && $order->vendor_id === $user->vendor->id;
        }

        return false;
    }

    /**
     * Determine whether the customer can pay for the order.
     */
    public function pay(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }
}
