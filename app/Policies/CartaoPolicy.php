<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\Cartao;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartaoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cartao $cartao): bool
    {
        return $user->id === $cartao->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cartao $cartao): bool
    {
        return false;
    }

    public function before(User $user, string $ability)
    {
        if($user->is_admin){
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cartao $cartao): bool
    {
        return $user->id === $cartao->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cartao $card): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cartao $card): bool
    {
        return false;
    }
}
