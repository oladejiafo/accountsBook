<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function access(User $user)
    {
        // Check if the user has access to the accounting module
        return $user->can('transactions.index');
    }

    public function viewAny(User $user)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return (bool) $user->isAccountant();

    //     $company = $user->company;
    //     if (!$company) {
    //         return false; // Or handle the scenario accordingly
    //     }

    //     // Fetch roles and permissions dynamically based on the company
    //     $roles = $company->roles;
    //     $permissions = $company->permissions;

    //     // Check if the user has any of the required roles or permissions
    //     foreach ($roles as $role) {
    //         if ($user->hasRole($role->name)) {
    //             return true;
    //         }
    //     }
    //     foreach ($permissions as $permission) {
    //         if ($user->hasPermission($permission->name)) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Transaction $transaction)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return (bool) $user->isAccountant();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return (bool) $user->isAccountant();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Transaction $transaction)
    {
        return (bool) $user->isAccountant();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Transaction $transaction)
    {
        return (bool) $user->isAccountant();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Transaction $transaction)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Transaction $transaction)
    {
        //
    }
}
