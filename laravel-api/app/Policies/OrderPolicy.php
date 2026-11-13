<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * ADMIN and USER can access the orders list.
     * The controller filters which orders USER can see.
     */
    public function viewAny(User $user): bool
    {
        if ($user->currentAccessToken() && ! $user->tokenCan('order:view')) {
            abort(403, 'Permission manquante');
        }

        return $user->hasRole('ADMIN')
            || $user->hasRole('USER');
    }

    /**
     * ADMIN can view every order.
     * USER can view only their own order.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->currentAccessToken() && ! $user->tokenCan('order:view')) {
            abort(403, 'Permission manquante');
        }

        if ($user->hasRole('ADMIN')) {
            return true;
        }

        if ($user->hasRole('USER')) {
            return ($order->user_id && $order->user_id == $user->id)
                || $order->client_id == $user->id
                || ($order->client && $order->client->email === $user->email);
        }

        return false;
    }

    /**
     * ADMIN and USER can create orders.
     */
    public function create(User $user): bool
    {
        if ($user->currentAccessToken() && ! $user->tokenCan('order:create')) {
            abort(403, 'Permission manquante');
        }

        return $user->hasRole('ADMIN')
            || $user->hasRole('USER');
    }

    /**
     * Only ADMIN can update orders.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->currentAccessToken() && ! $user->tokenCan('order:update')) {
            abort(403, 'Permission manquante');
        }

        return $user->hasRole('ADMIN');
    }

    /**
     * Only ADMIN can delete orders.
     */
    public function delete(User $user, Order $order): bool
    {
        if ($user->currentAccessToken() && ! $user->tokenCan('order:delete')) {
            abort(403, 'Permission manquante');
        }

        return $user->hasRole('ADMIN');
    }
}
