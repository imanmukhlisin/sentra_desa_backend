<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminActivationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@desa.com')->first();
        if ($admin) {
            $admin->is_active = true;
            $admin->user_level = 'superadmin';
            $admin->save();
            $role = Role::firstOrCreate(['name' => 'superadmin']);
            if (! $admin->hasRole('superadmin')) {
                $admin->assignRole($role);
            }
        }
    }
}
