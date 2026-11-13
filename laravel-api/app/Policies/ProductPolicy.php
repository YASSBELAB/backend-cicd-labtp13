<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a product.
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMIN');
    }

    /**
     * Determine whether the user can update a product.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('ADMIN');
    }

    /**
     * Determine whether the user can delete a product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('ADMIN');
    }
}
