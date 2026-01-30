<?php

namespace App\Policies;

use App\Models\AppraisalPeriod;
use App\Models\User;

class AppraisalPeriodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'hr_manager', 'staff', 'dept_head']); 
        // Everyone needs to see periods to query, but maybe only HR sees the Resource in nav?
        // Actually, Filament handles nav visibility via `canAccessPanel` usually or this policy.
        // Let's restrict *managing* to HR.
    }

    public function view(User $user, AppraisalPeriod $appraisalPeriod): bool
    {
        return true; 
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'hr_manager']);
    }

    public function update(User $user, AppraisalPeriod $appraisalPeriod): bool
    {
        return $user->hasRole(['super_admin', 'hr_manager']);
    }

    public function delete(User $user, AppraisalPeriod $appraisalPeriod): bool
    {
        return $user->hasRole(['super_admin', 'hr_manager']);
    }
}
