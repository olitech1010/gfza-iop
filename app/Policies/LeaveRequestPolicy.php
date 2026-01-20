<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaveRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_leave::request');
    }

    /**
     * Determine whether the user can view the model.
     * Staff can only view their own leave requests.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if (! $user->can('view_leave::request')) {
            return false;
        }

        // Staff can only view their own leave requests
        if ($user->hasRole('staff') && $leaveRequest->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_leave::request');
    }

    /**
     * Determine whether the user can update the model.
     * Staff can only update their own leave requests.
     */
    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        if (! $user->can('update_leave::request')) {
            return false;
        }

        // Staff can only update their own leave requests
        if ($user->hasRole('staff') && $leaveRequest->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('delete_leave::request');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_leave::request');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('force_delete_leave::request');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_leave::request');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('restore_leave::request');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_leave::request');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('replicate_leave::request');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_leave::request');
    }
}
