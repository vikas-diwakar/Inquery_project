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
     * Determine if the user can update the project
     */
    public function update(User $user, Project $project): bool
    {
        return $user->company_id === $project->company_id 
            && ($user->isAdmin() || $user->hasPermission('projects.edit'));
    }

    /**
     * Determine if the user can delete the project
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->company_id === $project->company_id && $user->isAdmin();
    }
}
