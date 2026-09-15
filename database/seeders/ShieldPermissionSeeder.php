<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShieldPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define all resources that need permissions
        $resources = [
            'merchant',
            'product',
            'user',
            'village::content',
        ];

        // Define permission prefixes
        $permissions = [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
        ];

        // Generate all permissions
        foreach ($resources as $resource) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission . '_' . $resource,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Add shield_admin permission
        Permission::firstOrCreate([
            'name' => 'shield_admin',
            'guard_name' => 'web',
        ]);

        // Assign ALL permissions to superadmin role
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $superadminRole->givePermissionTo(Permission::all());

        $this->command->info('✅ Shield permissions created and assigned to superadmin');
    }
}
