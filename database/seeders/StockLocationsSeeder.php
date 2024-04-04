<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockLocation;
class StockLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
// Define sample stock locations
$stockLocations = [
    ['name' => 'Main Store'],
    ['name' => 'Warehouse'],
    ['name' => 'Retail Outlet'],
    // Add more sample locations as needed
];

// Insert sample data into the database
foreach ($stockLocations as $location) {
    StockLocation::create($location);
}
    }
}
