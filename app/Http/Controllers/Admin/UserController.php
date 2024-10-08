<?php

namespace App\Http\Controllers\Admin;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Http\Controllers\MainController;
use App\Models\User;
use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use shared\ConfigDefaultInterface;
use Spatie\Permission\Models\Permission;

class UserController extends MainController
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'is_root')->where('is_blocked', false)->get();

        return view('main.admin.user.index', compact('users'));
    }

    public function create()
    {
        return view('main.admin.user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
        ]);

        $user = User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $request->input('is_admin')
            ? $user->assignRole(ConfigDefaultInterface::ROLE_ADMIN)
            : $user->assignRole(ConfigDefaultInterface::ROLE_USER);

        $keyField = OrderService::getKeyField();
        $user->fields()->sync($keyField);

        return redirect()->route('user.index')->with(
            ConfigDefaultInterface::FLASH_SUCCESS,
            sprintf('User %s created successfully', $user->name)
        );
    }

    public function edit(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found');
        }

        if ($user->is_root) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Admin root can not be edited');
        }

        // Get all permissions as a collection
        $allPermissions = Permission::all()->keyBy('name');
        // Use mapWithKeys to iterate over the permission groups and organize permissions
        $groupedPermissions = collect(ConfigDefaultInterface::PERMISSION_GROUPS)->mapWithKeys(function ($permissions, $groupName) use ($allPermissions) {
            // Filter the permissions that exist in the current group
            $groupPermissions = $allPermissions->whereIn('name', $permissions)->values();

            // Return the group name with its permissions if not empty
            return $groupPermissions->isNotEmpty() ? [$groupName => $groupPermissions] : [];
        });

        return view('main.admin.user.edit', compact('user', 'groupedPermissions'));
    }

    public function update(Request $request, int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->name = $validated['full_name'];
        $user->email = $validated['email'];
        $user->save();

        $request->input('is_admin')
            ? $this->updateRoleToAdmin($user)
            : $this->updateRoleToUser($user);

        return redirect()->route('user.index')->with(
            ConfigDefaultInterface::FLASH_SUCCESS,
            sprintf('User %s successfully updated', $user->name)
        );
    }

    public function assignFields(int $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found');
        }

        $mainTable = $this->factory()->createTableManagerAdmin()->getMainTable();
        if (!$mainTable) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Table not found');
        }

        $assignedFields = [];
        $notAssignedFields = [];


        foreach ($user->fields as $field) {
            $assignedFields[$field->id] = [
                'name' => $field->name,
                'type' => $field->type,
            ];
        }

        foreach ($mainTable->fields as $field) {
            if (!array_key_exists($field->id, $assignedFields)) {
                $notAssignedFields[$field->id] = [
                    'name' => $field->name,
                    'type' => $field->type,
                ];
            }
        }

        return view('main.admin.user.fields', compact('user', 'assignedFields', 'notAssignedFields'));
    }

    public function saveFields(Request $request)
    {
        $userId = $request->input('userId');
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found');
        }

        $assignedIdsString = $request->input('assignedIds');
        if ($assignedIdsString) {
            $assignedIdsArray = explode(',', $assignedIdsString);
            $assignedIdsArray = array_filter($assignedIdsArray, 'is_numeric');
            $assignedIdsArray = array_map('intval', $assignedIdsArray);


            if (!in_array(OrderService::getKeyField()->id, $assignedIdsArray)) {
                return redirect()->route('user.assign-fields', ['id'=>$user->id])->with(ConfigDefaultInterface::FLASH_ERROR, 'Key field can not be unassigned');
            }


            // Sync the fields with the user
            $user->fields()->sync($assignedIdsArray);
        } else {
            // key field should always be assigned
            return redirect()->route('user.assign-fields', ['id'=>$user->id])->with(ConfigDefaultInterface::FLASH_ERROR, 'Key field can not be unassigned');
        }

        // Redirect back with a success message
        return redirect()->back()->with(ConfigDefaultInterface::FLASH_SUCCESS, 'User fields have been successfully updated.');
    }

    public function givePermission(int $userId, string $permission) {
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found');
        }

        try {
            Permission::findByName($permission);
        } catch (\Exception) {
            return redirect()->route('user.edit', ['id' => $userId])->with(ConfigDefaultInterface::FLASH_ERROR, 'Unknown permission');
        }

        $user->givePermissionTo($permission);
        $this->logPermissionChanged($user, ActivityLogConstants::ACTION_GIVE_PERMISSION, $permission);

        return redirect()->route('user.edit', ['id' => $userId])->with(ConfigDefaultInterface::FLASH_SUCCESS, 'User permissions updated');
    }

    public function removePermission(int $userId, string $permission) {
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found');
        }

        try {
            Permission::findByName($permission);
        } catch (\Exception) {
            return redirect()->route('user.edit', ['id' => $userId])->with(ConfigDefaultInterface::FLASH_ERROR, 'Unknown permission');
        }

        $user->revokePermissionTo($permission);
        $this->logPermissionChanged($user, ActivityLogConstants::ACTION_REMOVE_PERMISSION, $permission);

        return redirect()->route('user.edit', ['id' => $userId])->with(ConfigDefaultInterface::FLASH_SUCCESS, 'User permissions updated');
    }



    private function updateRoleToAdmin(User $user): void
    {
        if (!$user->hasRole(ConfigDefaultInterface::ROLE_ADMIN)) {
            $user->removeRole(ConfigDefaultInterface::ROLE_USER);
            $user->assignRole(ConfigDefaultInterface::ROLE_ADMIN);

            $this->logUserRoleUpdatedToAdmin($user);
        }
    }

    private function updateRoleToUser(User $user): void
    {
        if (!$user->hasRole(ConfigDefaultInterface::ROLE_USER)) {
            $user->removeRole(ConfigDefaultInterface::ROLE_ADMIN);
            $user->assignRole(ConfigDefaultInterface::ROLE_USER);

            $this->logAdminRoleUpdatedToUser($user);
        }
    }

    private function logUserRoleUpdatedToAdmin(User $user): void
    {
        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(auth()->user()->email)
            ->setTitle(ActivityLogConstants::WARNING_LOG)
            ->setAction(ActivityLogConstants::ACTION_UPDATE)
            ->setOldData(sprintf('User %s (id:%s) role',$user->email, $user->id))
            ->setNewData('admin');

        $this->factory()->createActivityLogManager()->log($transfer);
    }

    private function logAdminRoleUpdatedToUser(User $user): void
    {
        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(auth()->user()->email)
            ->setTitle(ActivityLogConstants::WARNING_LOG)
            ->setAction(ActivityLogConstants::ACTION_UPDATE)
            ->setOldData(sprintf('User admin %s (id:%s) role',$user->email, $user->id))
            ->setNewData('user');

        $this->factory()->createActivityLogManager()->log($transfer);
    }

    private function logPermissionChanged(User $user, string $action, string $permission): void
    {
        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(auth()->user()->email)
            ->setTitle(ActivityLogConstants::WARNING_LOG)
            ->setAction($action)
            ->setNewData(sprintf('user %s (id:%s) to %s', $user->email, $user->id, $permission));

        $this->factory()->createActivityLogManager()->log($transfer);
    }
}
