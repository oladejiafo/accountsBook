<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        // if (!Auth::check()) {
        //     // Exclude the login route from redirection
        //     if ($request->routeIs('login')) {
        //         return $next($request);
        //     }
            
        //     // User is not authenticated, redirect to login page
        //     return redirect()->route('login');
        // }
    
        // Check if the user has permissions only for routes that require authentication
        if ($request->route() && $request->user()) {
            // Get all permissions from the database
            $permissions = Permission::pluck('name')->toArray();
           
            // Check if the authenticated user has any of the required permissions
            foreach ($permissions as $permission) {
                // dd($request->user(), $request->user()->hasPermission($permission));
                if ($request->user()->hasPermission($permission)) {
                    // dd('hi');
                    // User has permission, allow the request to proceed
                    return $next($request);
                }
            }
    
            // If none of the required permissions are found, abort the request with a 403 Forbidden response
            abort(403, 'Unauthorized');
        }
    
        // For routes that don't require authentication, proceed without permission checks
        return $next($request);
    }
    
}
