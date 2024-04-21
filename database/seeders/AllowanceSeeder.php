<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allowances = [
            [
                'name' => 'Housing Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Transport Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Meal Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Healthcare Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Education Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Phone Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Internet Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Travel Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Clothing Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Entertainment Allowance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            // Add more allowances as necessary
        ];
    
        foreach ($allowances as $allowance) {
            DB::table('allowances')->insert($allowance);
        }
    }
}
