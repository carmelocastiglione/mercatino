<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BookReservationBatch;

class BookReservationBatchPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user): ?bool
    {
        // Admin can do anything
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the batch.
     */
    public function view(User $user, BookReservationBatch $batch): bool
    {
        // Student can only view their own batches
        if ($user->role === 'studente') {
            return $user->id === $batch->user_id;
        }

        // Staff can view batches from their school
        if ($user->role === 'staff') {
            return $user->school_id === $batch->school_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the batch.
     */
    public function delete(User $user, BookReservationBatch $batch): bool
    {
        // Only the student who created it can delete it
        return $user->id === $batch->user_id && $batch->status === 'pending';
    }
}
