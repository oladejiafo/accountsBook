<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deductions = [
            [
                'name' => 'Income Tax',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Health Insurance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Pension Contribution',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Union Dues',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Loan Repayment',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Uniform Deduction',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Late Attendance',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Damage Deduction',
                'company_id' => 1,
                                // Add other fields as needed
            ],
            [
                'name' => 'Training Fees',
                'company_id' => 1,
                // Add other fields as needed
            ],
            [
                'name' => 'Other Deductions',
                'company_id' => 1,
                // Add other fields as needed
            ],
            // Add more deductions as necessary
        ];
    
        foreach ($deductions as $deduction) {
            DB::table('deductions')->insert($deduction);
        }
    }
}
