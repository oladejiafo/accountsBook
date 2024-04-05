<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            ['acronym' => 'USD', 'symbol' => '$'],
            ['acronym' => 'EUR', 'symbol' => '€'],
            ['acronym' => 'GBP', 'symbol' => '£'],
            ['acronym' => 'NGN', 'symbol' => '₦'],
            ['acronym' => 'KES', 'symbol' => 'KSh'],
            ['acronym' => 'GHS', 'symbol' => 'GH₵'],
            ['acronym' => 'RWF', 'symbol' => 'FRw'],
            ['acronym' => 'AED', 'symbol' => 'AED'],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
