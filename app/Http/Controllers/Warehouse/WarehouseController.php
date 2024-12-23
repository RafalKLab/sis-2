<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\MainController;
use App\Models\Note\Note;
use App\Models\Order\OrderItem;
use App\Models\User;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseItem;
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
        $warehouseItemsValueByName = [];
        foreach ($items['items'] as $item) {
            if (array_key_exists($item['name'], $warehouseItemsValueByName)) {
                $warehouseItemsValueByName[$item['name']]['total_price'] += $item['total_price'];
                $warehouseItemsValueByName[$item['name']]['amount'] += $item['amount'];
                $warehouseItemsValueByName[$item['name']]['unit'] = $item['measurement_unit'];
            } else {
                $warehouseItemsValueByName[$item['name']] = [
                    'total_price' => $item['total_price'],
                    'amount' => $item['amount'],
                    'unit' => $item['measurement_unit'],
                ];
            }
        }

        return view('main.user.warehouse.view', compact('warehouse', 'items', 'warehouseStockOverview', 'warehouseItemsValueByName'));
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

    public function updateTentativeDate(Request $request)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_MANAGE_WAREHOUSE_TENTATIVE_DATE)) {
            return redirect()->route('warehouses.index')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $validated = $request->validate([
            'item_id' => 'required',
            'warehouse_name' => 'required',
            'date' => 'required | date'
        ]);

        $item = OrderItem::find($validated['item_id']);
        if ($item === null) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'Item not found!');
        }

        WarehouseItem::updateOrCreate(
            [
                'order_item_id' => $validated['item_id'],
                'warehouse_name' => $validated['warehouse_name'],
            ],
            [
                'tentative_date' => $validated['date'],
            ]
        );

        return redirect()
            ->route('warehouses.view', ['name'=>$validated['warehouse_name']])
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, 'Date updated');
    }

    public function viewComments(int $warehouseId, int $itemId)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_MANAGE_WAREHOUSES)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $warehouse = Warehouse::find($warehouseId);
        if (!$warehouse) {
            return redirect()->route('warehouses.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Warehouse not found!');
        }
        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('warehouses.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item not found!');
        }

        $items = $this->factory()->createWarehouseManager()->collectWarehouseItems($warehouse, [$itemId]);
        $comments = $this->factory()->createNoteManager()->getNotesByIdentifierAndTarget(ConfigDefaultInterface::WAREHOUSE_ITEM_IDENTIFIER, $itemId);

        foreach ($comments as &$comment) {
            $comment['author_email'] = User::find($comment->author)->email;
        }
        unset($comment);

        return view('main.user.warehouse.comments', compact('items', 'warehouse', 'comments'));
    }

    public function addComment(Request $request ,int $warehouseId, int $itemId)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_WRITE_WAREHOUSE_ITEM_COMMENTS)) {
            return redirect()->route('warehouses.index')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $warehouse = Warehouse::find($warehouseId);
        if (!$warehouse) {
            return redirect()->route('warehouses.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Warehouse not found!');
        }
        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('warehouses.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item not found!');
        }

        $validate = $request->validate([
            'message' => 'required'
        ]);

        Note::create([
            'author' => Auth::user()->id,
            'message' => $validate['message'],
            'identifier' => ConfigDefaultInterface::WAREHOUSE_ITEM_IDENTIFIER,
            'target' => $itemId,
        ]);

        return redirect()
            ->route('warehouses.view-comments', ['warehouseId'=>$warehouseId, 'itemId'=>$itemId])
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, 'Comment created');
    }

    public function removeComment(int $noteId)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_DELETE_WAREHOUSE_ITEM_COMMENTS)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        };

        $this->factory()->createNoteManager()->deleteNote($noteId);

        return redirect()->back();
    }

}
