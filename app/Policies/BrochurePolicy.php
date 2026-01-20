<?php

namespace App\Policies;

use App\Models\Brochure;
use App\Models\User;

class BrochurePolicy
{
    /**
     * Determine if the user can delete the brochure
     */
    public function delete(User $user, Brochure $brochure): bool
    {
        return $user->company_id === $brochure->company_id 
            && ($user->isAdmin() || $user->hasPermission('brochures.delete'));
    }
}
