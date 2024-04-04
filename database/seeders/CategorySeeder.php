<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Clothing'],
            ['name' => 'Furniture'],
            ['name' => 'Books'],
            // Add more sample categories as needed
        ];

        // Insert the categories into the database
        DB::table('categories')->insert($categories);
    }
}
