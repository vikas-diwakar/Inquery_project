<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine if the user can view the project
     */
    public function view(User $user, Project $project): bool
    {
        return $user->company_id === $project->company_id;
    }

    /**
     * Determine if the user can create projects
     */
    public function create(User $user): bool
    {
        if ($user->role && $user->role->name === 'Sales Executive') {
            return false;
        }

        return $user->isAdmin() || $user->hasPermission('projects.create');
    }

    /**
     * Determine if the user can update the project
     */
    public function update(User $user, Project $project): bool
    {
        if ($user->role && $user->role->name === 'Sales Executive') {
            return false;
        }

        return $user->company_id === $project->company_id 
            && ($user->isAdmin() || $user->hasPermission('projects.edit'));
    }

    /**
     * Determine if the user can delete the project
     */
    public function delete(User $user, Project $project): bool
    {
        if ($user->role && $user->role->name === 'Sales Executive') {
            return false;
        }

        return $user->company_id === $project->company_id && $user->isAdmin();
    }
}
