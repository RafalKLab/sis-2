<?php

namespace App\Console\Commands\Admin;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use shared\ConfigDefaultInterface;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create main admin use';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $password = $this->argument('password');
        $email = 'admin@admin.com';
        $name = 'admin';

        if (!$password) {
            $this->error('Password is not provided');
        }

        $adminUser = User::where('email', $email)->first();
        if ($adminUser) {
            $this->error('Admin user already exists');

            return;
        }

        $adminUser = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $adminUser->assignRole(ConfigDefaultInterface::ROLE_ADMIN);

        $this->info('Admin user successfully created');
    }
}
