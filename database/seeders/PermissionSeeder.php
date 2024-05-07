<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *  php artisan db:seed --class=PermissionSeeder
     * @return void
     */
    public function run()
    {
        // Define permissions for Insight Module
        $this->createPermissions('Insight Module', [
            ['name' => 'dashboard_view', 'label' => 'View Insight Module'],
            ['name' => 'account_insight_view', 'label' => 'View Account Insight'],
            ['name' => 'inventory_insight_view', 'label' => 'View Inventory Insight'],
            ['name' => 'sales_insight_view', 'label' => 'View Sales Insight'],
            ['name' => 'payment_insight_view', 'label' => 'View Payment Insight'],
            ['name' => 'assets_insight_view', 'label' => 'View Assets Insight'],
            ['name' => 'employee_insight_view', 'label' => 'View Employee Insights'],
        ]);

        // Define permissions for Accounts Module
        $this->createPermissions('Accounts Module', [
            ['name' => 'ledger_view', 'label' => 'View Ledgers'],
            ['name' => 'transaction_view', 'label' => 'View Transactions'],
            ['name' => 'tax_rate_view', 'label' => 'View Tax Rates'],
            ['name' => 'tax_transaction_view', 'label' => 'View Tax Transactions'],
            ['name' => 'tax_payment_view', 'label' => 'View Tax Payments'],
            ['name' => 'tax_settings_view', 'label' => 'View Tax Settings'],
            ['name' => 'chart_of_account_view', 'label' => 'View Charts of Account'],
            ['name' => 'transaction_account_mapping_view', 'label' => 'View Transaction-Account Mapping'],
            ['name' => 'payroll_generation_view', 'label' => 'View Payroll Generation'],
            // Add other permissions as needed
        ]);

        // Define permissions for Banking Module
        $this->createPermissions('Banking Module', [
            ['name' => 'bank_feed_view', 'label' => 'View Bank Feeds'],
            ['name' => 'reconciliation_view', 'label' => 'View Reconciliations'],
            ['name' => 'transfer_view', 'label' => 'View Transfers'],
            // Add other permissions as needed
        ]);

        // Define permissions for Inventories Module
        $this->createPermissions('Inventories Module', [
            ['name' => 'stock_inventory_view', 'label' => 'View Stock Inventory'],
            ['name' => 'purchase_order_view', 'label' => 'View Purchase Orders'],
            ['name' => 'supplier_view', 'label' => 'View Suppliers'],
            ['name' => 'invoice_view', 'label' => 'View Invoices'],
            // Add other permissions as needed
        ]);

        // Define permissions for Customer Module
        $this->createPermissions('Customer Module', [
            ['name' => 'customer_view', 'label' => 'View Customers'],
            ['name' => 'sales_order_view', 'label' => 'View Sales Orders'],
            ['name' => 'payment_view', 'label' => 'View Payments'],
            ['name' => 'sales_return_view', 'label' => 'View Sales Returns'],
            ['name' => 'reciept_view', 'label' => 'View Invoices'],
            // Add other permissions as needed
        ]);

        // Define permissions for HR Module
        $this->createPermissions('HR Module', [
            ['name' => 'employee_record_view', 'label' => 'View Employee Records'],
            ['name' => 'payroll_view', 'label' => 'View Payroll'],
            // Add other permissions as needed
        ]);

        // Define permissions for Report Module
        $this->createPermissions('Report Module', [
            ['name' => 'account_report_view', 'label' => 'View Account Reports'],
            ['name' => 'banking_report_view', 'label' => 'View Banking Reports'],
            ['name' => 'ledger_report_view', 'label' => 'View Ledger Reports'],
            ['name' => 'inventory_report_view', 'label' => 'View Inventory Reports'],
            ['name' => 'customer_report_view', 'label' => 'View Customer Reports'],
            ['name' => 'hr_report_view', 'label' => 'View HR Reports'],
            // Add other permissions as needed
        ]);

        // Define permissions for Admin Settings Module
        $this->createPermissions('Admin Settings Module', [
            ['name' => 'settings_view', 'label' => 'View Settings'],
            ['name' => 'users_management_view', 'label' => 'View Users Management'],
            ['name' => 'roles_management_view', 'label' => 'View Roles Management'],
            ['name' => 'assign_permissions_view', 'label' => 'View Assign Permissions to Roles'],
            ['name' => 'backup_restore_view', 'label' => 'View Backup/Restore'],
            // Add other permissions as needed
        ]);

        // Define permissions for Accounts Module - Export, Create, Edit, Delete
        $this->createPermissions('Accounts Module', [
            ['name' => 'ledger_export', 'label' => 'Export Ledgers'],
            ['name' => 'transaction_export', 'label' => 'Export Transactions'],
            ['name' => 'tax_rate_create', 'label' => 'Create Tax Rates'],
            ['name' => 'tax_rate_edit', 'label' => 'Edit Tax Rates'],
            ['name' => 'tax_rate_delete', 'label' => 'Delete Tax Rates'],
            ['name' => 'tax_transaction_create', 'label' => 'Create Tax Transactions'],
            ['name' => 'tax_transaction_edit', 'label' => 'Edit Tax Transactions'],
            ['name' => 'tax_transaction_delete', 'label' => 'Delete Tax Transactions'],
            ['name' => 'tax_payment_create', 'label' => 'Create Tax Payments'],
            ['name' => 'tax_payment_edit', 'label' => 'Edit Tax Payments'],
            ['name' => 'tax_payment_delete', 'label' => 'Delete Tax Payments'],
            ['name' => 'tax_settings_export', 'label' => 'Export Tax Settings'],
            ['name' => 'chart_of_account_export', 'label' => 'Export Charts of Account'],
            ['name' => 'transaction_account_mapping_create', 'label' => 'Create Transaction-Account Mapping'],
            ['name' => 'transaction_account_mapping_edit', 'label' => 'Edit Transaction-Account Mapping'],
            ['name' => 'transaction_account_mapping_delete', 'label' => 'Delete Transaction-Account Mapping'],
            ['name' => 'payroll_generation_export', 'label' => 'Export Payroll Generation'],
        ]);

        // Define permissions for Banking Module - Export, Create, Edit, Delete
        $this->createPermissions('Banking Module', [
            ['name' => 'bank_feed_export', 'label' => 'Export Bank Feeds'],
            ['name' => 'reconciliation_export', 'label' => 'Export Reconciliations'],
            ['name' => 'transfer_create', 'label' => 'Create Transfers'],
            ['name' => 'transfer_edit', 'label' => 'Edit Transfers'],
            ['name' => 'transfer_delete', 'label' => 'Delete Transfers'],
        ]);

        // Define permissions for Inventories Module - Export, Create, Edit, Delete
        $this->createPermissions('Inventories Module', [
            ['name' => 'stock_inventory_export', 'label' => 'Export Stock Inventory'],
            ['name' => 'purchase_order_create', 'label' => 'Create Purchase Orders'],
            ['name' => 'purchase_order_edit', 'label' => 'Edit Purchase Orders'],
            ['name' => 'purchase_order_delete', 'label' => 'Delete Purchase Orders'],
            ['name' => 'supplier_export', 'label' => 'Export Suppliers'],
            ['name' => 'invoice_create', 'label' => 'Create Invoices'],
            ['name' => 'invoice_edit', 'label' => 'Edit Invoices'],
            ['name' => 'invoice_delete', 'label' => 'Delete Invoices'],
        ]);

        // Define permissions for Customer Module - Export, Create, Edit, Delete
        $this->createPermissions('Customer Module', [
            ['name' => 'customer_export', 'label' => 'Export Customers'],
            ['name' => 'sales_order_create', 'label' => 'Create Sales Orders'],
            ['name' => 'sales_order_edit', 'label' => 'Edit Sales Orders'],
            ['name' => 'sales_order_delete', 'label' => 'Delete Sales Orders'],
            ['name' => 'payment_create', 'label' => 'Create Payments'],
            ['name' => 'payment_edit', 'label' => 'Edit Payments'],
            ['name' => 'payment_delete', 'label' => 'Delete Payments'],
            ['name' => 'sales_return_create', 'label' => 'Create Sales Returns'],
            ['name' => 'sales_return_edit', 'label' => 'Edit Sales Returns'],
            ['name' => 'sales_return_delete', 'label' => 'Delete Sales Returns'],
        ]);

        // Define permissions for HR Module - Export, Create, Edit, Delete
        $this->createPermissions('HR Module', [
            ['name' => 'employee_record_export', 'label' => 'Export Employee Records'],
            ['name' => 'payroll_export', 'label' => 'Export Payroll'],
            ['name' => 'payroll_create', 'label' => 'Create Payroll'],
            ['name' => 'payroll_edit', 'label' => 'Edit Payroll'],
            ['name' => 'payroll_delete', 'label' => 'Delete Payroll'],
        ]);

        // Define permissions for Report Module - Export
        $this->createPermissions('Report Module', [
            ['name' => 'account_report_export', 'label' => 'Export Account Reports'],
            ['name' => 'banking_report_export', 'label' => 'Export Banking Reports'],
            ['name' => 'ledger_report_export', 'label' => 'Export Ledger Reports'],
            ['name' => 'inventory_report_export', 'label' => 'Export Inventory Reports'],
            ['name' => 'customer_report_export', 'label' => 'Export Customer Reports'],
            ['name' => 'hr_report_export', 'label' => 'Export HR Reports'],
        ]);

        // Define permissions for Admin Settings Module - Export, Create, Edit, Delete
        $this->createPermissions('Admin Settings Module', [
            ['name' => 'settings_export', 'label' => 'Export Settings'],
            ['name' => 'users_management_export', 'label' => 'Export Users Management'],
            ['name' => 'roles_management_export', 'label' => 'Export Roles Management'],
            ['name' => 'assign_permissions_export', 'label' => 'Export Assign Permissions to Roles'],
            ['name' => 'backup_restore_export', 'label' => 'Export Backup/Restore'],
            ['name' => 'backup_restore_create', 'label' => 'Create Backup/Restore'],
            ['name' => 'backup_restore_edit', 'label' => 'Edit Backup/Restore'],
            ['name' => 'backup_restore_delete', 'label' => 'Delete Backup/Restore'],
        ]);        
    }


    protected function createPermissions($moduleName, $permissions)
    {
        foreach ($permissions as $permission) {
            $permission['name'] = Str::slug($permission['name'], '_'); // Convert name to snake_case
            $permission['label'] = ucwords($permission['label']); // Capitalize label
            $permission['created_at'] = now();
            $permission['updated_at'] = now();
            $permission['module'] = $moduleName;
            DB::table('permissions')->insert($permission);
        }
    }
}
