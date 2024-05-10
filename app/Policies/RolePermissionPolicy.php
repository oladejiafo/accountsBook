<?php

namespace App\Policies;

use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePermissionPolicy
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
        || $user->hasPermission('role-permissions.index')
        ;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RolePermission  $rolePermission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, RolePermission $rolePermission)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('role-permissions.index')
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
        || $user->hasPermission('role-permissions.create')
        ;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RolePermission  $rolePermission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, RolePermission $rolePermission)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('role-permissions.update')
        ;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RolePermission  $rolePermission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, RolePermission $rolePermission)
    {
        return (bool) $user->hasRole('Super_Admin') 
        || $user->hasPermission('role-permissions.destroy')
        ;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RolePermission  $rolePermission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, RolePermission $rolePermission)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RolePermission  $rolePermission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, RolePermission $rolePermission)
    {
        //
    }
}
