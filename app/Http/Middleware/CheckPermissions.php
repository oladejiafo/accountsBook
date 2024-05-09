<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Route;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()) {
            $user = $request->user();
    
            // Exclude routes that should not be subject to permission check
            $excludedRoutes = [
                'login', 
                'logout', 
                'password.reset',
                'register.company',
                'register', 
                'about',
                'getSubModules',
                'fetchCustomerDetails',
                'fetchCustomerTransactions',
                'stocks.details',
                'getAccountClassifications',
                'autocomplete.customers',
                'global.search',
                'reconciliation.match',
                'returns.process', 
                'verification.notice',
                'verification.send',
                'verification.verify',
                'select-supplier',
                'password.email',
                'password.request',
                'password.update',
                'password.confirm',
            ]; 
    
            // Additional routes to exclude based on their ending
            $additionalExcludedRoutes = collect($excludedRoutes)->map(function ($route) {
                return $route . '.edit';
            })->merge(collect($excludedRoutes)->map(function ($route) {
                return $route . '.store';
            }))->merge(collect($excludedRoutes)->map(function ($route) {
                return $route . '.show';
            }))->merge(collect($excludedRoutes)->map(function ($route) {
                return $route . '.edit-stock';
            }))->merge(collect($excludedRoutes)->map(function ($route) {
                return $route . '.store-stock';
            }))->toArray();
    
            $excludedRoutes = array_merge($excludedRoutes, $additionalExcludedRoutes);
    
            $routeName = $request->route()->getName();
    
            if (!in_array($routeName, $excludedRoutes)) {
                $companyId = auth()->user()->company_id;
    
                // Fetch all roles associated with the user within the context of the user's company
                $roles = $user->roles()->where('roles.company_id', $companyId)->pluck('id')->toArray();
    
                // Fetch permissions associated with these roles
                $permissions = RolePermission::join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                                ->whereIn('role_permissions.role_id', $roles)
                                ->where('role_permissions.company_id', $companyId)
                                ->distinct()
                                ->pluck('permissions.name');
    
                // Check if the permission for the current route exists among the user's permissions
                $hasPermission = $permissions->contains($routeName);
    
                if (!$hasPermission && !$user->hasRole('Super_Admin')) {
                    abort(403, 'Unauthorized');
                }
            }
        }
    
        return $next($request);
    }    

}
