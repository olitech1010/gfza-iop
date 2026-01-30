<?php

namespace App\Policies;

use App\Models\Appraisal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppraisalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; 
        // Everyone can access the Resource, but the Query Scope will filter what they see.
        // HOD sees department, Staff sees own, HR sees all.
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appraisal $appraisal): bool
    {
        // HR can see all
        if ($user->hasRole(['super_admin', 'hr_manager'])) {
            return true;
        }

        // HOD can see their department/assigned reviews
        if ($appraisal->hod_id === $user->id) {
            return true;
        }

        // Staff can see their own
        return $appraisal->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Staff / Regular users create appraisals for themselves?
        // Usually yes, at the start of the period.
        return true; 
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appraisal $appraisal): bool
    {
        if ($user->hasRole(['super_admin'])) {
            return true;
        }

        switch ($appraisal->status) {
            case 'goal_setting':
                // Staff can edit their own *during* goal setting
                return $user->id === $appraisal->user_id;
            
            case 'hod_review':
                // HOD can edit *during* hod_review
                return $user->id === $appraisal->hod_id;

            case 'hr_review':
                // HR can edit/finalize *during* hr_review
                return $user->hasRole('hr_manager');

            default:
                return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appraisal $appraisal): bool
    {
        return $user->hasRole(['super_admin', 'hr_manager']);
    }
}
