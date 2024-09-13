<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\MainController;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use shared\ConfigDefaultInterface;

class WarehouseController extends MainController
{
    public function index()
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_MANAGE_WAREHOUSES)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $warehouses = Warehouse::all();

        $productsInStock = $this->factory()->createWarehouseManager()->collectWarehouseInStockItemCount();

        return view('main.user.warehouse.index', compact('warehouses', 'productsInStock'));
    }

    public function view(string $name)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_MANAGE_WAREHOUSES)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $warehouse = Warehouse::where('name', $name)->first();
        if (!$warehouse) {
            return redirect()->route('warehouses.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Warehouse not found!');
        }

        $items = $this->factory()->createWarehouseManager()->collectWarehouseItems($warehouse);
        $warehouseStockOverview = $this->factory()->createWarehouseManager()->collectWarehouseStockOverview($warehouse->id);

        return view('main.user.warehouse.view', compact('warehouse', 'items', 'warehouseStockOverview'));
    }

    public function create(Request $request)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_MANAGE_WAREHOUSES)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('warehouses'),
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'is_active' => [
                'required',
                'string',
                'in:0,1',
            ],
        ]);

        $warehouse = Warehouse::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('warehouses.index')
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('New warehouse: %s created', $warehouse->name));
    }

    public function update(Request $request, string $name)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_MANAGE_WAREHOUSES)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $warehouse = Warehouse::where('name', $name)->first();
        if (!$warehouse) {
            return redirect()->route('warehouses.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Warehouse not found!');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('warehouses')->ignore($warehouse->id),
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'is_active' => [
                'required',
                'string',
                'in:0,1',
            ],
        ]);

        $warehouse->update($validated);

        return redirect()
            ->route('warehouses.view', ['name'=>$warehouse->name])
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Warehouse updated'));
    }
}
