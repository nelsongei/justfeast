<?php

namespace App\Policies;

use App\Models\Vendor;
use App\Models\User;

class VendorPolicy
{
    /**
     * Determine whether the user can view any vendors.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a vendor.
     */
    public function view(?User $user, Vendor $vendor): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the vendor account.
     */
    public function update(User $user, Vendor $vendor): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'vendor' && $vendor->user_id === $user->id;
    }
}
