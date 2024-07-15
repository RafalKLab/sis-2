<?php

namespace App\Http\Controllers\Admin;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Http\Controllers\MainController;
use App\Models\MainTable;
use App\Models\Table\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use shared\ConfigDefaultInterface;

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

        return view('main.admin.user.edit', compact('user'));
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
            $assignedFields[$field->id] = $field->name;
        }

        foreach ($mainTable->fields as $field) {
            if (!array_key_exists($field->id, $assignedFields)) {
                $notAssignedFields[$field->id] = $field->name;
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

            // Sync the fields with the user
            $user->fields()->sync($assignedIdsArray);
        } else {
            // If the string is null or empty, detach all fields
            $user->fields()->detach();
        }

        // Redirect back with a success message
        return redirect()->back()->with(ConfigDefaultInterface::FLASH_SUCCESS, 'User fields have been successfully updated.');
    }

    private function updateRoleToAdmin($user): void
    {
        if (!$user->hasRole(ConfigDefaultInterface::ROLE_ADMIN)) {
            $user->removeRole(ConfigDefaultInterface::ROLE_USER);
            $user->assignRole(ConfigDefaultInterface::ROLE_ADMIN);

            $this->logUserRoleUpdatedToAdmin($user);
        }
    }

    private function updateRoleToUser($user): void
    {
        if (!$user->hasRole(ConfigDefaultInterface::ROLE_USER)) {
            $user->removeRole(ConfigDefaultInterface::ROLE_ADMIN);
            $user->assignRole(ConfigDefaultInterface::ROLE_USER);

            $this->logAdminRoleUpdatedToUser($user);
        }
    }

    private function logUserRoleUpdatedToAdmin($user): void
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

    private function logAdminRoleUpdatedToUser($user): void
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
}
