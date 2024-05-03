<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *  php artisan db:seed --class=RolePermissionSeeder
     * @return void
     */
    // public function run()
    // {
    //     // Admin Role Permissions
    //     $this->assignPermissions(1, [
    //         'insight_view',
    //         'account_view',
    //         'banking_view',
    //         'inventory_view',
    //         'customer_view',
    //         'hr_view',
    //         'report_view',
    //         'admin_settings_view',
    //     ]);

    //     // Accounts Role Permissions
    //     $this->assignPermissions(2, [
    //         'account_view',
    //     ]);

    //     // HR Role Permissions
    //     $this->assignPermissions(3, [
    //         'hr_view',
    //     ]);
    // }

    // protected function assignPermissions($roleId, $permissions)
    // {
    //     foreach ($permissions as $permission) {
    //         $permissionId = DB::table('permissions')->where('name', $permission)->value('id');

    //         if ($permissionId) {
    //             DB::table('role_permissions')->insert([
    //                 'role_id' => $roleId,
    //                 'permission_id' => $permissionId,
    //                 'company_id' => 1, // Assuming company_id = 1 for all roles
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }
    //     }
    // }

    public function run()
    {
        $superAdminRole = Role::where('name', 'Super_Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            DB::table('role_permissions')->insert([
                'role_id' => $superAdminRole->id,
                'permission_id' => $permission->id,
                'company_id' => $superAdminRole->company_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('role_permissions')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
                'company_id' => $adminRole->company_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
