<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'vendor' && $user->vendor !== null;
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'vendor' && $user->vendor && $product->vendor_id === $user->vendor->id;
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'vendor' && $user->vendor && $product->vendor_id === $user->vendor->id;
    }
}
