<?php

namespace App\Policies;

use App\Models\Acquisition;
use App\Models\User;

class AcquisitionPolicy
{
    /**
     * Determine if the user can view the acquisition.
     */
    public function view(User $user, Acquisition $acquisition): bool
    {
        return $user->id === $acquisition->seller_id;
    }
}
