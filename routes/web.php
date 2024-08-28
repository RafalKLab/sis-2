<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminFieldController;
use App\Http\Controllers\Admin\AdminTableController;
use App\Http\Controllers\Admin\BlockedUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\FieldSettings\FieldSettingsController;
use App\Http\Controllers\File\FileController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Note\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Statistics\StatisticsController;
use App\Http\Controllers\Statistics\UserStatisticsController;
use App\Http\Controllers\Warehouse\WarehouseController;
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

    /* Customer table */
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    /* Invoice table */
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

    /* Notes */
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('/notes/delete', [NoteController::class, 'destroy'])->name('notes.destroy');

    /* Order item */
    Route::get('/orders/{id}/add-item', [OrderController::class, 'addItem'])->name('orders.add-item');
    Route::post('/orders/{id}/add-item', [OrderController::class, 'storeItem'])->name('orders.store-item');
    Route::get('/orders/{orderId}/edit-item/{itemId}', [OrderController::class, 'editItem'])->name('orders.edit-item');
    Route::put('/orders/{orderId}/edit-item/{itemId}', [OrderController::class, 'updateItem'])->name('orders.update-item');
    Route::get('/orders/{orderId}/remove-item/{itemId}', [OrderController::class, 'removeItem'])->name('orders.remove-item');

    /* Item buyer */
    Route::get('/orders/{orderId}/item/{itemId}/add-buyer', [OrderController::class, 'addBuyer'])->name('orders.add-item-buyer');
    Route::post('/orders/{orderId}/item/{itemId}/add-buyer', [OrderController::class, 'storeBuyer'])->name('orders.store-item-buyer');
    Route::get('/orders/{orderId}/item/{itemId}/edit-buyer/{buyerId}', [OrderController::class, 'editBuyer'])->name('orders.edit-item-buyer');
    Route::put('/orders/{orderId}/item/{itemId}/edit-buyer/{buyerId}', [OrderController::class, 'updateBuyer'])->name('orders.update-item-buyer');
    Route::get('/orders/{orderId}/item/{itemId}/remove-buyer/{buyerId}', [OrderController::class, 'removeBuyer'])->name('orders.remove-item-buyer');

    /* User orders table */
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/register', [OrderController::class, 'register'])->name('orders.register');
    Route::post('/orders/register', [OrderController::class, 'registerConfirm'])->name('orders.register-confirm');
    Route::get('/orders/{id}', [OrderController::class, 'view'])->name('orders.view');
    Route::get('/orders/edit/{id}', [OrderController::class, 'edit'])->name('orders.edit');
    Route::get('/orders/edit/{orderId}/field/{fieldId}', [OrderController::class, 'editField'])->name('orders.edit-field');
    Route::post('/orders/edit/{id}', [OrderController::class, 'update'])->name('orders.update');

    /* Customer invoices */
    Route::get('/orders/edit/{orderId}/customer-invoice/{customer}', [OrderController::class, 'editCustomerInvoice'])->name('orders.edit-customer-invoice');
    Route::post('/orders/edit/{orderId}/customer-invoice/{customer}', [OrderController::class, 'saveCustomerInvoice'])->name('orders.save-customer-invoice');

    /* Warehouse */
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('/warehouses/{name}', [WarehouseController::class, 'view'])->name('warehouses.view');
    Route::put('/warehouses/{name}', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::post('/warehouses', [WarehouseController::class, 'create'])->name('warehouses.create');

    /* Api route for order select creation */
    Route::get('/api/orders', [OrderController::class, 'orders'])->name('api.orders');

    /* Api route for field settings */
    Route::post('/settings/field/toggle-auto-calculations', [FieldSettingsController::class, 'toggleFieldAutoCalculations'])->name('field.toggle-auto-calculations');

    /* Order file upload */
    Route::get('/order/{orderId}/files', [FileController::class, 'index'])->name('order-files.index');
    Route::get('/order/{orderId}/upload', [FileController::class, 'upload'])->name('order-files.upload');
    Route::post('/order/upload', [FileController::class, 'store'])->name('order-files.store');
    Route::get('/order/file/{fileId}/show', [FileController::class, 'show'])->name('order-files.show');
    Route::delete('/order/file/{fileId}/delete', [FileController::class, 'delete'])->name('order-files.delete');

    /* Admin-only Routes */
    Route::middleware(['role:admin'])->group(function () {
        /* User management */
        Route::get('/admin/users', [UserController::class, 'index'])->name('user.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/admin/users/create', [UserController::class, 'store'])->name('user.store');
        Route::get('/admin/users/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/admin/users/edit/{id}', [UserController::class, 'update'])->name('user.update');
        Route::get('/admin/users/assign-fields/{id}', [UserController::class, 'assignFields'])->name('user.assign-fields');
        Route::post('/admin/users/save-fields', [UserController::class, 'saveFields'])->name('user.save-fields');
        Route::get('/admin/users/{userId}/give-permission/{permission}', [UserController::class, 'givePermission'])->name('user.give-permission');
        Route::get('/admin/users/{userId}/remove-permission/{permission}', [UserController::class, 'removePermission'])->name('user.remove-permission');

        /* Blocked users */
        Route::get('/admin/blocked-users', [BlockedUserController::class, 'index'])->name('user-blocked.index');
        Route::get('/admin/users/block/{id}', [BlockedUserController::class, 'block'])->name('user-blocked.block');
        Route::get('/admin/users/unblock/{id}', [BlockedUserController::class, 'unblock'])->name('user-blocked.unblock');

        /* Activity logs */
        Route::get('/admin/logs', [ActivityLogController::class, 'index'])->name('logs.index');

        /* Admin table */
        /* Will be removed */
//        Route::get('/admin/table', [AdminTableController::class, 'index'])->name('admin-table.index');

        /* Admin table fields */
        Route::get('/admin/fields', [AdminFieldController::class, 'index'])->name('admin-fields.index');
        Route::post('/admin/fields', [AdminFieldController::class, 'index'])->name('admin-fields.change-table');
        Route::get('/admin/fields/create', [AdminFieldController::class, 'create'])->name('admin-fields.create');
        Route::post('/admin/fields/create', [AdminFieldController::class, 'store'])->name('admin-fields.store');
        Route::get('/admin/fields/{id}', [AdminFieldController::class, 'show'])->name('admin-fields.show');
        Route::get('/admin/fields/edit/{id}', [AdminFieldController::class, 'edit'])->name('admin-fields.edit');
        Route::put('/admin/fields/edit/{id}', [AdminFieldController::class, 'update'])->name('admin-fields.update');
        Route::get('/admin/fields/move-up/{id}', [AdminFieldController::class, 'moveUpFieldOrder'])->name('admin-fields.move-up');
        Route::get('/admin/fields/move-down/{id}', [AdminFieldController::class, 'moveDownFieldOrder'])->name('admin-fields.move-down');

        /* Statistics */
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
        Route::post('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');

        Route::get('/statistics/users', [UserStatisticsController::class, 'index'])->name('statistics-user.index');
        Route::post('/statistics/users', [UserStatisticsController::class, 'index'])->name('statistics-user.index');
        Route::get('/statistics/users/{userId}/{year}/{month}', [UserStatisticsController::class, 'show'])->name('statistics-user.show');
        Route::post('/statistics/users/{userId}/{year}/{month}', [UserStatisticsController::class, 'show'])->name('statistics-user.show');

    });
});

require __DIR__.'/auth.php';
