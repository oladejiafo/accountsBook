<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChartOfAccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //chartOfAccounts
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('chartOfAccounts')
        ;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ChartOfAccount $chartOfAccount)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('chartOfAccounts')
        ;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('chartOfAccounts.create')
        ;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ChartOfAccount $chartOfAccount)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('chartOfAccounts.update')
        ;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ChartOfAccount $chartOfAccount)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('chartOfAccounts.destroy')
        ;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ChartOfAccount $chartOfAccount)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ChartOfAccount $chartOfAccount)
    {
        //
    }
}
