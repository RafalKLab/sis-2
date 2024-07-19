<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use shared\ConfigDefaultInterface;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        foreach (ConfigDefaultInterface::AVAILABLE_PERMISSIONS as $permission) {
            try {
                Permission::create(['name' => $permission]);
            } catch (\Exception) {
            }
        }
    }
}
