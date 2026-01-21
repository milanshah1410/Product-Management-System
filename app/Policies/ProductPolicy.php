<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * View any products (index)
     */
    public function viewAny(User $user): bool
    {
        return true; // all logged-in users
    }

    /**
     * View single product (show)
     */
    public function view(User $user, Product $product): bool
    {
        return true; // public or logged-in users
    }

    /**
     * Create product
     */
    public function create(User $user): bool
    {
        return $user->can('manage products') || $user->role === 'admin';
    }

    /**
     * Update product
     */
    public function update(User $user, Product $product): bool
    {
        return
            $user->can('manage products') ||
            ($product->user_id === $user->id);
    }

    /**
     * Delete product
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }
}
