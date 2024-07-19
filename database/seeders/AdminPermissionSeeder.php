<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use shared\ConfigDefaultInterface;
use Spatie\Permission\Models\Role;

class AdminPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::findByName(ConfigDefaultInterface::ROLE_ADMIN);

        foreach (ConfigDefaultInterface::AVAILABLE_PERMISSIONS as $permission) {
            $adminRole->givePermissionTo($permission);
        }
    }
}
