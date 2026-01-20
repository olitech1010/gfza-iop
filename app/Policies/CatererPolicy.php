<?php

namespace App\Policies;

use App\Models\Caterer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatererPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_caterer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Caterer $caterer): bool
    {
        return $user->can('view_caterer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_caterer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Caterer $caterer): bool
    {
        return $user->can('update_caterer');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Caterer $caterer): bool
    {
        return $user->can('delete_caterer');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_caterer');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Caterer $caterer): bool
    {
        return $user->can('force_delete_caterer');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_caterer');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Caterer $caterer): bool
    {
        return $user->can('restore_caterer');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_caterer');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Caterer $caterer): bool
    {
        return $user->can('replicate_caterer');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_caterer');
    }
}
