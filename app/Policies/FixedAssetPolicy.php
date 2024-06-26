<?php

namespace App\Policies;

use App\Models\FixedAsset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FixedAssetPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        if ($user->hasRole('Super_Admin')) {
            return true;
        }
    
        // Get all roles of the user
        $roles = $user->roles->pluck('name')->toArray();
    
        // Custom logic based on roles and company context
        foreach ($roles as $role) {
            if ($this->canViewForRole($role)) {
                return true;
            }
        }
    
        return false;
    }
    
    public function view(User $user, FixedAsset $asset)
    {
        return (bool) $user->hasRole('Super_Admin') ||  $user->hasPermission('fixedAsset.index');
    }
    
    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return (bool) $user->hasRole('Super_Admin') ||  $user->hasPermission('fixedAsset.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, FixedAsset $asset)
    {
        return (bool) $user->hasRole('Super_Admin') ||  $user->hasPermission('fixedAsset.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, FixedAsset $asset)
    {
        return (bool) $user->hasRole('Super_Admin') ||  $user->hasPermission('fixedAsset.destroy');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, FixedAsset $asset)
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
    public function forceDelete(User $user, FixedAsset $asset)
    {
        //
    }
}
