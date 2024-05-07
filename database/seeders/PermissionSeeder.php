<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = [
            'Insight Module',
            'Accounts Module',
            'Banking Module',
            'Inventories Module',
            'Customer Module',
            'HR Module',
            'Report Module',
            'Admin Settings Module',
        ];

        $permissions = ['view', 'edit', 'create', 'delete', 'export'];

        foreach ($modules as $module) {
            foreach ($permissions as $permission) {
                $this->createPermission($permission, $module);
            }
        }
    }

    protected function createPermission($permission, $module)
    {
        $permissionLabel = ucfirst($permission) . ' ' . $module;

        DB::table('permissions')->insert([
            'name' => $permission,
            'label' => $permissionLabel,
            'module' => $module,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
