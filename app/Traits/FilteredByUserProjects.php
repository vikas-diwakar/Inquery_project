<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilteredByUserProjects
{
    /**
     * Boot the trait
     * Apply filtering to only show projects assigned to the user (unless admin)
     */
    public static function bootFilteredByUserProjects(): void
    {
        static::addGlobalScope('user_projects', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Admins can see all projects
                if ($user->isAdmin()) {
                    return;
                }

                // Non-admins can only see projects assigned to them
                $assignedProjectIds = $user->projects()->pluck('projects.id')->toArray();
                if (!empty($assignedProjectIds)) {
                    $builder->whereIn('id', $assignedProjectIds);
                } else {
                    // If user has no assigned projects, show no projects
                    $builder->whereRaw('1 = 0');
                }
            }
        });
    }

    /**
     * Scope to get projects assigned to a specific user
     */
    public function scopeAssignedToUser(Builder $query, $user = null)
    {
        $user = $user ?? auth()->user();

        if ($user && !$user->isAdmin()) {
            $assignedProjectIds = $user->projects()->pluck('projects.id')->toArray();
            if (!empty($assignedProjectIds)) {
                return $query->whereIn('id', $assignedProjectIds);
            } else {
                return $query->whereRaw('1 = 0');
            }
        }

    }

}