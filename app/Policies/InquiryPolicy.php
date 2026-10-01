<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;

class InquiryPolicy
{
    /**
     * Determine if the user can view the inquiry.
     * Admin can view all company inquiries.
     * Non-admin users (Sales Executives) can ONLY view inquiries for projects assigned to them.
     */
    public function view(User $user, Inquiry $inquiry): bool
    {
        if ($user->company_id !== $inquiry->company_id) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        // Must be assigned to the project the inquiry belongs to
        return $user->projects()->where('projects.id', $inquiry->project_id)->exists();
    }

    /**
     * Determine if the user can update the inquiry.
     */
    public function update(User $user, Inquiry $inquiry): bool
    {
        if ($user->company_id !== $inquiry->company_id) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        // Must be assigned to the project and have edit permission
        return $user->projects()->where('projects.id', $inquiry->project_id)->exists()
            && ($user->hasPermission('inquiries.edit') || in_array($user->role->name ?? '', ['Manager', 'Sales Executive']));
    }

    /**
     * Determine if the user can delete the inquiry.
     * Only Admin can delete inquiries.
     */
    public function delete(User $user, Inquiry $inquiry): bool
    {
        return $user->company_id === $inquiry->company_id && $user->isAdmin();
    }
}