<?php

namespace App\Http\Controllers\Admin;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use shared\ConfigDefaultInterface;

class BlockedUserController extends MainController
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')->where('is_blocked', true)->get();

        return view('main.admin.user.blocked', compact('users'));
    }

    public function block(int $userId) {
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found');
        }

        if ($user->is_root) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Admin root can not be blocked');
        }

        if ($user->is_blocked) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User is already blocked');
        }

        // Assuming you're using the database session driver and the user model has a relationship with sessions
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        $user->is_blocked = true;
        $user->remember_token = null;
        $user->save();

        $this->logUserBlocked($user);

        return redirect()->route('user.index')
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('User %s (id: %s) blocked', $user->email, $user->id));
    }

    public function unblock(int $userId) {
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found');
        }

        if (!$user->is_blocked) {
            return redirect()->route('user.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User is not blocked');
        }

        $user->is_blocked = false;
        $user->save();

        $this->logUserUnblocked($user);

        return redirect()->route('user-blocked.index')
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('User %s (id: %s) unblocked', $user->email, $user->id));
    }


    private function logUserBlocked($user): void
    {
        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(auth()->user()->email)
            ->setTitle(ActivityLogConstants::DANGER_LOG)
            ->setAction(ActivityLogConstants::ACTION_BLOCKED)
            ->setNewData(sprintf(' user %s (id:%s) ',$user->email, $user->id));

        $this->factory()->createActivityLogManager()->log($transfer);
    }

    private function logUserUnblocked($user)
    {
        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(auth()->user()->email)
            ->setTitle(ActivityLogConstants::DANGER_LOG)
            ->setAction(ActivityLogConstants::ACTION_UNBLOCKED)
            ->setNewData(sprintf(' user %s (id:%s) ',$user->email, $user->id));

        $this->factory()->createActivityLogManager()->log($transfer);
    }
}
