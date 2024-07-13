<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminTableController;
use App\Http\Controllers\Admin\BlockedUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticatedSessionController::class, 'create']);

Route::get('/dashboard', function () {
    return view('main.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    /* Profile */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* Admin-only Routes */
    Route::middleware(['role:admin'])->group(function () {
        /* User management */
        Route::get('/admin/users', [UserController::class, 'index'])->name('user.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/admin/users/create', [UserController::class, 'store'])->name('user.store');
        Route::get('/admin/users/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/admin/users/edit/{id}', [UserController::class, 'update'])->name('user.update');

        /* Blocked users */
        Route::get('/admin/blocked-users', [BlockedUserController::class, 'index'])->name('user-blocked.index');
        Route::get('/admin/users/block/{id}', [BlockedUserController::class, 'block'])->name('user-blocked.block');
        Route::get('/admin/users/unblock/{id}', [BlockedUserController::class, 'unblock'])->name('user-blocked.unblock');

        /* Activity logs */
        Route::get('/admin/logs', [ActivityLogController::class, 'index'])->name('logs.index');

        /* Admin table */
        Route::get('/admin/table', [AdminTableController::class, 'index'])->name('admin-table.index');
    });
});

require __DIR__.'/auth.php';
