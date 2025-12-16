<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function before(User $user, string $ability)
    {
        if($user->is_admin){
            return true;
        }
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin ?? false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return ($user->is_admin ?? false) || $user->id===$model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $userLogado): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $userLogado, User $model): bool
    {
        return $userLogado->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $userLogado, User $userADeletar): bool
    {
        return $userLogado->is_admin && $userLogado->id !== $userADeletar->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $userLogado, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $userLogado, User $model): bool
    {
        return false;
    }
}
