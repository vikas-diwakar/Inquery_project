<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;

class InquiryPolicy
{
    /**
     * Determine if the user can view the inquiry
     */
    public function view(User $user, Inquiry $inquiry): bool
    {
        return $user->company_id === $inquiry->company_id;
    }

    /**
     * Determine if the user can update the inquiry
     */
    public function update(User $user, Inquiry $inquiry): bool
    {
        return $user->company_id === $inquiry->company_id 
            && ($user->isAdmin() || $user->hasPermission('inquiries.edit'));
    }

    /**
     * Determine if the user can delete the inquiry
     */
    public function delete(User $user, Inquiry $inquiry): bool
    {
        return $user->company_id === $inquiry->company_id && $user->isAdmin();
    }
}
