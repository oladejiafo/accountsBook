<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\Company;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;


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
                } else {
                    $companyId = 1;
                    $companyName = "Demo";
                    $companyBusiness = "Services";
                    $companySubscription = "demo";
                    $companyAddress = "Demo Avenue, IL";
                    $companyPhone = "+1 (444) 89787878";
                    $companyWebsite = "www.afriledger.com";
                    $companyEmail = "info@afriledger.com";
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
            }
    
            // $view->with('companyName', $companyName);
            $view->with([
                'companyName' => $companyName,
                'companyBusiness' => $companyBusiness,
                'companySubscription' => $companySubscription,
                'companyAddress' => $companyAddress,
                'companyPhone' => $companyPhone,
                'companyWebsite' => $companyWebsite,
                'companyEmail' => $companyEmail,
            ]);
        });
    }
}
