<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *  php artisan db:seed --class=ModuleSeeder
     * @return void
     */
    public function run()
    {

        // Seed data for Insight Module
        $this->createModuleWithSubModules('Insight Module', [
            'Account - main dashboard',
            'Inventory Insight',
            'Sales Insight',
            'Payment Insight',
            'Assets Insight',
            'Employee Insights',
        ]);

        // Seed data for Accounts Module
        $this->createModuleWithSubModules('Accounts Module', [
            'Ledgers',
            'Transactions',
            'Tax Rates',
            'Tax Transactions',
            'Tax Payments',
            'Tax Settings',
            'Charts of Account',
            'Transaction-Account Mapping',
            'Payroll Generation',
        ]);

        // Seed data for Banking Module
        $this->createModuleWithSubModules('Banking Module', [
            'Bank Feeds',
            'Reconciliations',
            'Transfer/Deposit/Withdrawal',
        ]);

        // Seed data for Inventories Module
        $this->createModuleWithSubModules('Inventories Module', [
            'Stock Inventory',
            'Purchase Order',
            'Suppliers',
            'Invoices',
        ]);

        // Seed data for Customer Module
        $this->createModuleWithSubModules('Customer Module', [
            'Customers',
            'Sales Order',
            'Payments',
            'Sales Returns',
            'Invoices',
        ]);

        // Seed data for HR Module
        $this->createModuleWithSubModules('HR Module', [
            'Employee Records',
            'Payroll',
        ]);

        // Seed data for Report Module
        $this->createModuleWithSubModules('Report Module', [
            'Account Reports',
            'Banking Reports',
            'Ledger Reports',
            'Inventory Reports',
            'Customer Reports',
            'HR Reports',
        ]);

        // Seed data for Admin Settings Module
        $this->createModuleWithSubModules('Admin Settings Module', [
            'Settings',
            'Users Management',
            'Roles Management',
            'Assign Permissions to Roles',
            'Backup/Restore',
        ]);
    }

    protected function createModuleWithSubModules($moduleName, $subModules)
    {
        $moduleId = DB::table('modules')->insertGetId(['name' => $moduleName]);

        foreach ($subModules as $subModuleName) {
            DB::table('sub_modules')->insert([
                'module_id' => $moduleId,
                'name' => $subModuleName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
