<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all registered routes
        $routes = collect(Route::getRoutes())->map->getName()->reject(function ($name) {
            return is_null($name); // Filter out routes with no name
        })->unique();

        // Create permissions based on route names
        foreach ($routes as $routeName) {
            Permission::create([
                'name' => $routeName,
                'label' => 'Access ' . $routeName, // You can adjust the label as needed
                'module' => 'General', // Adjust the module name if necessary
            ]);
        }
    }
}
