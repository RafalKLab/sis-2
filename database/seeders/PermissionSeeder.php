<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use shared\ConfigDefaultInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        foreach (ConfigDefaultInterface::AVAILABLE_PERMISSIONS as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
