<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine if the user can view the project.
     * Admin has access to all company projects.
     * Non-admin users (such as Sales Executives) can ONLY access their assigned projects.
     */
    public function view(User $user, Project $project): bool
    {
        if ($user->company_id !== $project->company_id) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        // Non-admin users can ONLY view/access projects assigned to them
        return $user->projects()->where('projects.id', $project->id)->exists();
    }

    /**
     * Determine if the user can create projects.
     * Sales Executives cannot create projects.
     */
    public function create(User $user): bool
    {
        if ($user->role && $user->role->name === 'Sales Executive') {
            return false;
        }

        return $user->isAdmin() || $user->hasPermission('projects.create');
    }

    /**
     * Determine if the user can update the project.
     * Sales Executives cannot update projects.
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
     * Determine if the user can delete the project.
     * Only Admin can delete projects.
     */
    public function delete(User $user, Project $project): bool
    {
        if ($user->role && $user->role->name === 'Sales Executive') {
            return false;
        }

        return $user->company_id === $project->company_id && $user->isAdmin();
    }
}