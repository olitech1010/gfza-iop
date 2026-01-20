<?php

namespace App\Policies;

use App\Models\RoomBooking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomBookingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_room::booking');
    }

    /**
     * Determine whether the user can view the model.
     * Staff can only view their own bookings.
     */
    public function view(User $user, RoomBooking $roomBooking): bool
    {
        if (! $user->can('view_room::booking')) {
            return false;
        }

        // Staff can only view their own bookings
        if ($user->hasRole('staff') && $roomBooking->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_room::booking');
    }

    /**
     * Determine whether the user can update the model.
     * Staff can only update their own bookings.
     */
    public function update(User $user, RoomBooking $roomBooking): bool
    {
        if (! $user->can('update_room::booking')) {
            return false;
        }

        // Staff can only update their own bookings
        if ($user->hasRole('staff') && $roomBooking->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     * Staff can only delete their own bookings.
     */
    public function delete(User $user, RoomBooking $roomBooking): bool
    {
        if (! $user->can('delete_room::booking')) {
            return false;
        }

        // Staff can only delete their own bookings
        if ($user->hasRole('staff') && $roomBooking->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_room::booking');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, RoomBooking $roomBooking): bool
    {
        return $user->can('force_delete_room::booking');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_room::booking');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, RoomBooking $roomBooking): bool
    {
        return $user->can('restore_room::booking');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_room::booking');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, RoomBooking $roomBooking): bool
    {
        return $user->can('replicate_room::booking');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_room::booking');
    }
}
