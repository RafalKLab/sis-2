<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hamcrest\Core\AllOf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use shared\ConfigDefaultInterface;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name','email')->get();

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

    private function updateRoleToAdmin($user): void
    {
        if (!$user->hasRole(ConfigDefaultInterface::ROLE_ADMIN)) {
            $user->removeRole(ConfigDefaultInterface::ROLE_USER);
            $user->assignRole(ConfigDefaultInterface::ROLE_ADMIN);
        }
    }

    private function updateRoleToUser($user): void
    {
        if (!$user->hasRole(ConfigDefaultInterface::ROLE_USER)) {
            $user->removeRole(ConfigDefaultInterface::ROLE_ADMIN);
            $user->assignRole(ConfigDefaultInterface::ROLE_USER);
        }
    }
}
