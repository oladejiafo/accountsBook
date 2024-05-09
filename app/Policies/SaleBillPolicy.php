<?php

namespace App\Policies;

use App\Models\SaleBill;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SaleBillPolicy
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
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('sales.index')
        ;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SaleBill  $saleBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SaleBill $saleBill)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('sales.index')
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
        || $user->hasPermission('sales.create')
        ;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SaleBill  $saleBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SaleBill $saleBill)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('sales.update')
        ;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SaleBill  $saleBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SaleBill $saleBill)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('sales.destroy')
        ;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SaleBill  $saleBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SaleBill $saleBill)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SaleBill  $saleBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SaleBill $saleBill)
    {
        //
    }
}
