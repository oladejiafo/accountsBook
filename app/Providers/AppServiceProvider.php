<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\View;
use App\Models\Company;
use App\Models\Currency;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Policies\DynamicAuthorizationPolicy;
use App\Models\User;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::policy(User::class, DynamicAuthorizationPolicy::class);

        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $companyId = null;
            $companyName = null;
            $companyBusiness = null;
            $companySubscription = null;
            $companyAddress = null;
            $companyPhone = null;
            $companyWebsite = null;
            $companyEmail = null;
            $companyCurrency = null;
            $defaultCurrency = null;
    
            if (auth()->check()) {
                $companyId = auth()->user()->company_id;
                $company = Company::find($companyId);
    
                if ($company) {
                    $companyName = $company->name;
                    $companyBusiness = $company->business_type;
                    $companySubscription = $company->subscription_type;
                    $companyAddress = $company->address;
                    $companyPhone = $company->phone;
                    $companyWebsite = $company->website;
                    $companyEmail = $company->email;
                    $companyCurrency = $company->currency;
                    
                } else {
                    $companyId = 1;
                    $companyName = "Demo";
                    $companyBusiness = "Services";
                    $companySubscription = "demo";
                    $companyAddress = "Demo Avenue, IL";
                    $companyPhone = "+1 (444) 89787878";
                    $companyWebsite = "www.afriledger.com";
                    $companyEmail = "info@afriledger.com";
                    $companyCurrency = "NGN";
                }
            } else {
                $companyId = 1;
                $companyName = "Demo";
                $companyBusiness = "Services";
                $companySubscription = "demo";
                $companyAddress = "Demo Avenue, IL";
                $companyPhone = "+1 (444) 89787878";
                $companyWebsite = "www.afriledger.com";
                $companyEmail = "info@afriledger.com";
                $companyCurrency = "NGN";
            }
    
            $currencies = Currency::where('acronym','=', $companyCurrency)->pluck('symbol')->first();

            if(isset($currencies)){
                $defaultCurrency = $currencies;
            } else {
                $defaultCurrency = "$";
            }
            // $view->with('companyName', $companyName);
            $view->with([
                'companyId' => $companyId,
                'companyName' => $companyName,
                'companyBusiness' => $companyBusiness,
                'companySubscription' => $companySubscription,
                'companyAddress' => $companyAddress,
                'companyPhone' => $companyPhone,
                'companyWebsite' => $companyWebsite,
                'companyEmail' => $companyEmail,
                'defaultCurrency' => $defaultCurrency,
            ]);
        });
    }
}
