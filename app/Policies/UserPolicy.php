<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can update the user
     */
    public function update(User $user, User $model): bool
    {
        return $user->company_id === $model->company_id 
            && ($user->isAdmin() || $user->id === $model->id);
    }

    /**
     * Determine if the user can delete the user
     */
    public function delete(User $user, User $model): bool
    {
        return $user->company_id === $model->company_id 
            && $user->isAdmin() 
            && $user->id !== $model->id;
    }
}
