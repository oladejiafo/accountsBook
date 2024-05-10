<?php

namespace App\Policies;

use App\Models\PurchaseBill;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseBillPolicy
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
        || $user->hasPermission('purchase.index')
        ;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PurchaseBill  $purchaseBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PurchaseBill $purchaseBill)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('purchase.index')
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
        || $user->hasPermission('purchase.create')
        ;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PurchaseBill  $purchaseBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PurchaseBill $purchaseBill)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('purchase.update')
        ;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PurchaseBill  $purchaseBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PurchaseBill $purchaseBill)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('purchase.destroy')
        ;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PurchaseBill  $purchaseBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, PurchaseBill $purchaseBill)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PurchaseBill  $purchaseBill
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, PurchaseBill $purchaseBill)
    {
        //
    }
}
