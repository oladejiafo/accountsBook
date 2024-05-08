<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\DynamicAuthorizationPolicy;
use App\Policies\UserPolicy;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */

    protected $policies = [
        User::class => UserPolicy::class,
        Transaction::class => TransactionPolicy::class,
        // 'App\Models\User' => 'App\Policies\DynamicAuthorizationPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        // Gate::policy(DynamicAuthorizationPolicy::class, DynamicAuthorizationPolicy::class);

        // \Log::info('DynamicAuthorizationPolicy registered successfully.');
    }
}
