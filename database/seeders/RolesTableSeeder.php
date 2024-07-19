<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use shared\ConfigDefaultInterface;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        foreach (ConfigDefaultInterface::AVAILABLE_ROLES as $role) {
            try {
                Role::create(['name' => $role]);
            } catch (\Exception) {
            }
        }
    }
}
