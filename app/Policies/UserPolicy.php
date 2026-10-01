<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     * Only Admin can access user management.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view a specific user.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->company_id === $model->company_id;
    }

    /**
     * Determine if the user can create users.
     * Only Admin can create users.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the user.
     * Only Admin can update users.
     */
    public function update(User $user, User $model): bool
    {
        return $user->company_id === $model->company_id && $user->isAdmin();
    }

    /**
     * Determine if the user can delete the user.
     * Only Admin can delete users (and cannot delete own account).
     */
    public function delete(User $user, User $model): bool
    {
        return $user->company_id === $model->company_id 
            && $user->isAdmin() 
            && $user->id !== $model->id;
    }
}