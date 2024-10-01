<?php

namespace App\Console\Commands\Admin;

use App\Business\BusinessFactory;
use App\Models\Table\TableField;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    protected array $users;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "users.json";

        if (!is_readable($filename)) {
            exit("Error: File does not exist or cannot be read.");
        }

        $jsonContent = file_get_contents($filename);
        if ($jsonContent === false) {
            exit("Error: Failed to read from file.");
        }

        // Convert the JSON string back to an associative array.
        // The second parameter `true` ensures that the returned objects will be converted into associative arrays.
        $array = json_decode($jsonContent, true);

        // Check if json_decode returns NULL due to an error in decoding JSON.
        if ($array === null && json_last_error() !== JSON_ERROR_NONE) {
            exit("Error: Failed to decode JSON. Error - " . json_last_error_msg());
        }

        $this->users = $array;

        $this->createUsers();

        $this->info('Users created');
    }

    private function createUsers(): void
    {
        // Field map
        $fieldNameIdMap = [];
        $tableId = (new BusinessFactory())->createTableManagerAdmin()->getMainTable()->id;

        $fields = TableField::where('table_id', $tableId)->get();
        foreach ($fields as $field) {
            $fieldNameIdMap[$field->name] = $field->id;
        }

        // Create user
        foreach ($this->users as $userData) {
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                ]);
            }

            // Assign roles if needed
            foreach ($userData['roles'] as $role) {
                $user->assignRole($role);
            }

            // Assign permissions
            foreach ($userData['permissions'] as $permission) {
                $user->givePermissionTo($permission);
            }

            // Assign fields
            if (in_array('all', $userData['fields'])) {
                $fieldIds = TableField::where('table_id', '1')->pluck('id')->toArray();
                $user->fields()->sync($fieldIds);
            } else {
                $mappedFields = array_map(function ($fieldName) use ($fieldNameIdMap) {
                    // Return the ID corresponding to the field name, or null if not found
                    return $fieldNameIdMap[$fieldName] ?? null;
                }, $userData['fields']);

                $user->fields()->sync($mappedFields);
            }
        }
    }
}
