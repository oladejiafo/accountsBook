<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Routes to exempt
        $exemptRoutes = [
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
    
        // Get all registered routes
        $routes = collect(Route::getRoutes())->map->getName()->reject(function ($name) use ($exemptRoutes) {
            return is_null($name) || in_array($name, $exemptRoutes) || $this->shouldSkip($name); // Filter out routes with no name or in the exempt list
        })->unique();
    
        // Create permissions based on route names
        foreach ($routes as $routeName) {
            // Separate hyphenated or dotted words, replace with space, and capitalize each word
            $label = ucwords(preg_replace('/[-.]/', ' ', $routeName));
        
            // Replace "index" with "View"
            $label = str_replace(' Index', ' View', $label);
    
            // Replace "destroy" with "delete"
            $routeName = str_replace('destroy', 'delete', $routeName);
        
            Permission::create([
                'name' => $routeName,
                'label' => $label,
                'module' => 'General', // Adjust the module name if necessary
            ]);
        }
    }
    
    // Function to determine if a route should be skipped
    private function shouldSkip($routeName)
    {
        // Exempt routes ending with "edit" or "store"
        if (Str::endsWith($routeName, ['edit', 'store','show','edit-stock', 'store-stock'])) {
            return true;
        }
    
        return false;
    }
    
}
