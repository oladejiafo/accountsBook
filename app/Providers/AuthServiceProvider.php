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
        Supplier::class => SupplierPolicy::class, 
        Stock::class => StockPolicy::class, 
        SaleBill::class => SaleBillPolicy::class, 
        Role::class => RolePolicy::class, 
        RolePermission::class => RolePermissionPolicy::class, 
        PurchaseBill::class => PurchaseBillPolicy::class, 
        Payment::class => PaymentPolicy::class, 
        Customer::class => CustomerPolicy::class, 
        ChartOfAccount::class => ChartOfAccountPolicy::class,
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

    }
}
