<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User|Account $user): bool
    {
        return $user instanceof Account || $user instanceof Account || $user->can('view_any_lead');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User|Account $user, Account $account): bool
    {
        return $user instanceof Account || $user->can('view_lead');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User|Account $user): bool
    {
        return $user instanceof Account || $user->can('create_lead');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Account $user, Account $account): bool
    {
        return $user instanceof Account || $user->can('update_lead');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Account $user, Account $account): bool
    {
        return $user instanceof Account || $user->can('delete_lead');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User|Account $user): bool
    {
        return $user instanceof Account || $user->can('delete_any_lead');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User|Account $user, Account $account): bool
    {
        return $user instanceof Account || $user->can('force_delete_lead');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User|Account $user): bool
    {
        return $user instanceof Account || $user->can('force_delete_any_lead');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User|Account $user, Account $account): bool
    {
        return $user instanceof Account || $user->can('restore_lead');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User|Account $user): bool
    {
        return $user instanceof Account || $user->can('restore_any_lead');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User|Account $user, Account $account): bool
    {
        return $user instanceof Account || $user->can('replicate_lead');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User|Account $user): bool
    {
        return $user instanceof Account || $user->can('reorder_lead');
    }
}
