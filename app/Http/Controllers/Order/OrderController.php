<?php

namespace App\Http\Controllers\Order;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Business\Table\Config\ItemsTableConfig;
use App\Http\Controllers\MainController;
use App\Models\Company\Company;
use App\Models\Order\Comment;
use App\Models\Order\Invoice;
use App\Models\Order\ItemBuyer;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\TableField;
use App\Models\Warehouse\Warehouse;
use App\Service\InvoiceService;
use App\Service\OrderService;
use App\Service\TableService;
use App\Service\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use shared\ConfigDefaultInterface;

class OrderController extends MainController
{
    public function orderTree(int $orderId) {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->back();
        }

        $orderHierarchy  = $this->factory()->createOrderManager()->getRelatedOrderHierarchy($order);

        return view('main.user.order.tree.tree', compact('orderHierarchy'));
    }

    public function index(Request $request)
    {
        // Start time
        $start_time = microtime(true);

        $search = $request->search;
        $tableData = $this->factory()->createTableManager()->retrieveTableData($search);
        if ($tableData['exact_match']) {
            return redirect()->route('orders.view', ['id'=>$tableData['exact_match']]);
        }

        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        return view('main.user.order.index', compact('tableData', 'search', 'execution_time'));
    }

    public function view(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $canSeeOrder = true;

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                $canSeeOrder = false;
            }
        }

        if (Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_RELATED_ORDERS)) {
            $rootOrder = OrderService::getRootOrder($orderId);
            if ($rootOrder->user_id === Auth::user()->id) {
                $canSeeOrder = true;
            }
        }

        if (!$canSeeOrder) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);

        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];

        return view('main.user.order.show', compact('orderData', 'excludedFieldsForDetails'));
    }

    public function edit(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $orderFormData = $this->factory()->createOrderManager()->getOrderDetails($order);


        // Remove invoice fields from form data
        $filtered = array_filter($orderFormData['details'], function ($item) {
            return $item['field_type'] !== ConfigDefaultInterface::FIELD_TYPE_INVOICE;
        });

        $orderFormData['details'] = $filtered;

        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];

        return view('main.user.order.edit', compact('orderData', 'orderFormData', 'excludedFieldsForDetails'));
    }

    public function editField(int $orderId, int $fieldId) {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $orderFormData = $this->factory()->createOrderManager()->getOrderDetails($order);

        $targetField = null;
        foreach ($orderFormData['details'] as $fieldData) {
            if ($fieldData['field_id'] === $fieldId) {
                $targetField = $fieldData;

                break;
            }
        }

        if ($targetField) {
            $orderFormData['details'] = [
                $targetField
            ];
        }

        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];

        return view('main.user.order.edit', compact('orderData', 'orderFormData', 'excludedFieldsForDetails'));
    }

    public function update(Request $request, int $orderId)
    {
        $updatedFields = 0;

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        // Get all input data
        $allInputs = $request->all();

        // Filter inputs that start with 'field_'
        $fieldInputs = array_filter($allInputs, function($key) {
            return strpos($key, 'field_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        // Update each field accordingly
        foreach ($fieldInputs as $key => $value) {
            // Remove 'field_' to get just the numeric ID and find order data for that field
            $fieldId = preg_replace('/[^0-9]/', '', $key);
            $orderData = OrderData::where('order_id', $orderId)->where('field_id', $fieldId)->first();

            // Create or update invoice entity
            if (TableService::getFieldById($fieldId)->type === ConfigDefaultInterface::FIELD_TYPE_INVOICE) {
                $updatedFields = $this->executeInvoiceUpdate($request, $fieldId, $updatedFields, $orderId, $value);
            }

            if ($orderData) {
                // If value in request is different from old value then update
                // If null replace with empty
                $value = (string) $value;
                if ($orderData->value !== $value) {
                    $orderData->update([
                        'value' => $value,
                        'last_updated_by_user_id' => Auth::user()->id,
                    ]);

                    $updatedFields++;
                }
            } else {
                // Create new order data entity only if value is not null
                if ($value) {
                    $data = [
                        'value' => $value,
                        'field_id' => $fieldId,
                    ];
                    $order->data()->create($data);
                    $updatedFields++;
                }
            }
        }

        // Mark order as updated
        if ($updatedFields) {
            $order->touch();
            $this->executeOrderCalculations($order);

            return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Successfully updated %s fields', $updatedFields));
        } else {
            return redirect()->route('orders.view', ['id'=>$orderId]);
        }
    }

    public function updateCompany(Request $request, int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $validated = $request->validate([
            'company' => [
                'required',
                'numeric',
                'exists:companies,id'
            ],
        ]);
        $oldCompany = (string) $order->company?->name;

        $order->company_id = $validated['company'];
        $order->save();
        $order->load('company');

        $newCompany = $order->company?->name;
        if ($oldCompany !== $newCompany) {
            $this->logOrderCompanyChanged($order, $oldCompany, $newCompany);
        }


        return redirect()->route('orders.view', ['id' => $order->id])->with(ConfigDefaultInterface::FLASH_SUCCESS, 'Order company context updated');
    }

    public function register()
    {
        $user = auth()->user();
        if (!($user->hasRole(ConfigDefaultInterface::ROLE_ADMIN) || $user->hasPermissionTo(ConfigDefaultInterface::PERMISSION_REGISTER_ORDER))) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission to register new order');
        }

        $orderKeyFieldId = OrderService::getKeyField()->id;
        $relatedOrders = OrderData::where('field_id', $orderKeyFieldId)->orderBy('value', 'desc')->pluck('value', 'order_id')->toArray();
        $companies = Company::all()->pluck('name', 'id')->toArray();

        return view('main.user.order.register', compact('relatedOrders', 'companies'));
    }

    public function registerConfirm(Request $request)
    {
        $validated = $request->validate([
            'related_order' => [
                'required',
                'numeric',
                Rule::when($request->related_order != 0, [
                    'exists:orders,id'
                ]),
            ],
            'company' => [
                'required',
                'numeric',
                'exists:companies,id'
            ],
        ]);

        $parentOrder = Order::find($validated['related_order']);

        $newOrder = Order::create([
            'parent_id' => $parentOrder?->id,
            'user_id' => auth()->user()->id,
            'company_id' => $validated['company'],
        ]);

        $this->factory()->createOrderScoreCalculator()->calculateOrderScore($newOrder);

        $copyRelatedOrder = $request->copy_related_order;

        if ($parentOrder && $copyRelatedOrder) {

            // Copy related order items
            $parentItems = $parentOrder->items;

            foreach ($parentItems as $parentItem) {

                $newItem = OrderItem::create([
                    'order_id' => $newOrder->id,
                ]);

                foreach ($parentItem->data as $parentItemData) {
                    $newItem->data()->create([
                        'value' => $parentItemData->value,
                        'field_id' => $parentItemData->field_id,
                    ]);
                }

            }

        }

        return redirect()->route('orders.view', ['id' => $newOrder->id])->with(ConfigDefaultInterface::FLASH_SUCCESS, 'Order registered');
    }

    /* Api call for select2 in register order page */
    public function orders(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page');
        $orderKeyFieldId = OrderService::getKeyField()->id; // Get the specific key field id

        // Query to fetch related orders based on the field_id and filtering by search term if provided
        $query = OrderData::where('field_id', $orderKeyFieldId);

        if ($search) {
            $query->where('value', 'like', '%' . $search . '%'); // Assuming 'value' is the searchable field that contains the order name or similar
        }

        $orders = $query->orderBy('value', 'desc')
            ->paginate(10, ['order_id', 'value'], 'page', $page);

        // Format results for Select2
        $results = $orders->map(function ($order) {
            return ['id' => $order->order_id, 'text' => $order->value];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $orders->hasMorePages()
            ]
        ]);
    }

    public function addItem(int $orderId) {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_ADD_ORDER_PRODUCTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $orderFormData = $this->factory()->createOrderManager()->getItemFormData();
        $itemFromWarehouse = false;
        $excludedFields = TableService::getExcludedItemFields($itemFromWarehouse);
        $lockedFields = TableService::getLockedFields();
        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];


        return view(
            'main.user.order.add-item',
            compact('orderData', 'orderFormData', 'itemFromWarehouse', 'excludedFields', 'lockedFields','excludedFieldsForDetails'),
        );
    }

    public function addItemFromWarehouse(int $orderId) {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_ADD_ORDER_PRODUCTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $warehouseItems = $this->factory()->createWarehouseManager()->collectAllWarehouseItems();

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $orderFormData = $this->factory()->createOrderManager()->getItemFormData();
        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];


        return view('main.user.order.add-item-from-warehouse', compact('orderData', 'orderFormData', 'warehouseItems', 'excludedFieldsForDetails'));
    }

    public function storeItemFromWarehouse(Request $request, int $orderId) {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_ADD_ORDER_PRODUCTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        // Initial Validation
        $validator = Validator::make($request->all(), [
            'warehouse' => 'required',
            'item' => 'required',
            'amount' => 'required|numeric|gt:0',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Additional Validation: Check if warehouse exists
        $warehouse = Warehouse::where('name', $request->warehouse)->first();
        if (!$warehouse) {
            // Manually add an error to the validator for the warehouse field
            $validator->errors()->add('warehouse', 'The selected warehouse does not exist');

            // Redirect back with the new error
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $itemFromWarehouse = OrderItem::find($request->item);
        if (!$itemFromWarehouse) {
            // Manually add an error to the validator for the warehouse field
            $validator->errors()->add('item', 'The selected item does not exist');

            // Redirect back with the new error
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check quantity in warehouse
        $warehouseId = $this->getItemFieldDataByType($request->item, ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE)?->value;
        $itemFromWarehouseAmount = WarehouseService::getItemStock($itemFromWarehouse->id, $warehouseId);

        if ($request->amount > $itemFromWarehouseAmount) {
            // Manually add an error to the validator for the warehouse field
            $validator->errors()->add('amount', 'Insufficient product stock in warehouse');

            // Redirect back with the new error
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $newItem = OrderItem::create([
            'order_id' => $orderId,
            'is_taken_from_warehouse' => true,
            'parent_item' => $itemFromWarehouse->id,
        ]);


        $lockedFields = TableService::getLockedFields();
        $duplicateFields = TableService::getDuplicateFields();
        foreach ($itemFromWarehouse->data as $itemData)
        {
            if (in_array($itemData->field_id, $lockedFields)) {
                $newItem->data()->create([
                    'value' => $itemData->value,
                    'field_id' => $itemData->field_id,
                ]);
            }

            if (in_array($itemData->field_id, $duplicateFields)) {
                $newItem->data()->create([
                    'value' => $itemData->value,
                    'field_id' => $itemData->field_id,
                ]);
            }
        }

        // Set amount from warehouse
        $amountFromWarehouseFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_TYPE_AMOUNT_FROM_WAREHOUSE)->id;
        $newItem->data()->create([
            'value' => (float) $request->amount,
            'field_id' => $amountFromWarehouseFieldId,
        ]);

        // Set available amount from warehouse
        $amountFromWarehouseFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_TYPE_AVAILABLE_AMOUNT_FROM_WAREHOUSE)->id;
        $newItem->data()->create([
            'value' => (float) $request->amount,
            'field_id' => $amountFromWarehouseFieldId,
        ]);

        // Set item prime cost from warehouse
        $itemPrice = (float) $this->getItemFieldDataByType($itemFromWarehouse->id, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_NUMBER)?->value;
        $itemPrimeCost = $this->factory()->createWarehouseManager()->calculateItemPrimeCost($itemPrice, $itemFromWarehouse->id);

        $itemPrimeCostFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_TYPE_ITEM_PRIME_COST)->id;
        $newItem->data()->create([
            'value' => $itemPrimeCost,
            'field_id' => $itemPrimeCostFieldId,
        ]);

        // Lock parent item
        $itemFromWarehouse->is_locked = true;
        $itemFromWarehouse->save();

        WarehouseService::updateItemWarehouseStock($newItem);

        return redirect()->route('orders.edit-item', [
            'orderId' => $orderId,
            'itemId' => $newItem->id,
        ]);
    }

    public function storeItem(Request $request, int $orderId) {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_ADD_ORDER_PRODUCTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        // Get all input data
        $allInputs = $request->all();

        // Filter inputs that start with 'field_'
        $fieldInputs = array_filter($allInputs, function($key) {
            return strpos($key, 'field_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        if (!empty($fieldInputs)) {
            // create order item entity
            $orderItem = OrderItem::create(['order_id' => $orderId]);
        } else {
            return redirect()->route('orders.view', ['id' => $orderId]);
        }

        foreach ($fieldInputs as $key => $value) {
            // Remove 'field_' to get just the numeric ID and find order data for that field
            $fieldId = preg_replace('/[^0-9]/', '', $key);
            // If value in request is different from old value then update
            // If null replace with empty
            $value = (string) $value;

            // Create new order data entity only if value is not null
            if ($value) {
                $data = [
                    'value' => $value,
                    'field_id' => $fieldId,
                ];
                $orderItem->data()->create($data);
            }
        }

        $this->executeItemCalculations($orderItem);

        $this->logItemAdded($order, $orderItem);

        WarehouseService::updateItemWarehouseStock($orderItem);

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('New item was added to order'));
    }

    public function unlockItem(int $orderId, int $itemId) {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_EDIT_ORDER_PRODUCTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_UNLOCK_ITEM)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item not found');
        }

        $item->is_locked = false;
        $item->save();

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);

        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];

        return view('main.user.order.show', compact('orderData', 'excludedFieldsForDetails'));
    }

    public function editItem(int $orderId, int $itemId)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_EDIT_ORDER_PRODUCTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item not found');
        }

        if ($item->is_locked) {
            return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_ERROR, 'Item is locked and can not be modified');
        }

        $itemFromWarehouse = $item->is_taken_from_warehouse;
        $isEdit = true;
        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $orderFormData = $this->extractTargetItem($orderData, $itemId);
        $excludedFields = TableService::getExcludedItemFields($itemFromWarehouse);
        $lockedFields = TableService::getLockedFields();

        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];

        return view('main.user.order.edit-item',
            compact('orderData', 'orderFormData', 'isEdit', 'itemId', 'itemFromWarehouse', 'excludedFields', 'lockedFields', 'excludedFieldsForDetails'),
        );
    }

    public function updateItem(Request $request, int $orderId, int $itemId)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_EDIT_ORDER_PRODUCTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        // Get all input data
        $allInputs = $request->all();

        // Filter inputs that start with 'field_'
        $fieldInputs = array_filter($allInputs, function($key) {
            return strpos($key, 'field_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        foreach ($fieldInputs as $key => $value) {
            // Remove 'field_' to get just the numeric ID and find order data for that field
            $fieldId = preg_replace('/[^0-9]/', '', $key);

            $itemData = OrderItemData::where('field_id', $fieldId)->where('order_item_id', $itemId)->first();
            if ($itemData) {
                // If value in request is different from old value then update
                // If null replace with empty
                $value = (string) $value;
                if ($itemData->value !== $value) {
                    $itemData->update([
                        'value' => $value,
                        'last_updated_by_user_id' => Auth::user()->id,
                    ]);

                }
            } else {
                // Create new order data entity only if value is not null
                if ($value) {
                    $data = [
                        'value' => $value,
                        'field_id' => $fieldId,
                    ];
                    $item->data()->create($data);
                }
            }
        }

        $this->executeItemCalculations($item);

        $item->refresh();
        if (!$item->is_taken_from_warehouse)
        {
            WarehouseService::updateItemWarehouseStock($item);
        }

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Item was updated '));
    }

    public function removeItem(int $orderId, int $itemId) {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_REMOVE_ORDER_PRODUCTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission for this action');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item not found');
        }

        if ($item->is_locked) {
            return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_ERROR, 'Item is locked and can not be modified');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $this->logItemRemoved($order, $item);

        $item->delete();

        $this->executeItemCalculations($item);

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Item was removed'));
    }

    public function addBuyer(int $orderId, int $itemId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item not found');
        }

        if ($item->is_locked) {
            return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_ERROR, 'Item is locked and can not be modified');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $availableBuyers = ItemBuyer::query()->distinct()->pluck('name')->toArray();

        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];

        if ($item->is_taken_from_warehouse) {
            $availableItemQuantity = (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_AVAILABLE_AMOUNT_FROM_WAREHOUSE)?->value;
        } else {
            $availableItemQuantity = (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE)?->value;
        }

        $countryMap = ConfigDefaultInterface::ORDER_COUNTRY_MAP;

        // Retrieve carriers
        $fieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_CARRIER)->id;
        $itemCarriers = $this->factory()->createOrderManager()->getDynamicSelectOptionsByField($fieldId, ItemsTableConfig::TABLE_NAME);
        $buyerCarriers = ItemBuyer::whereNotNull('carrier')->pluck('carrier')->unique()->toArray();
        $carriers = array_merge($itemCarriers, $buyerCarriers);
        $carriers = array_unique($carriers);

        return view(
            'main.user.order.add-buyer',
            compact('orderData', 'itemId', 'orderId', 'availableBuyers', 'availableItemQuantity', 'excludedFieldsForDetails', 'countryMap', 'carriers'),
        );
    }

    public function storeBuyer(Request $request, int $orderId) {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        // Validation rules
        $validatedData = $request->validate([
            'buyer' => 'required',
            'quantity' => 'required|numeric|gt:0', // Ensure quantity is a number and at least 1
            'itemId' => 'required',
            'address' => 'max:255',
            'carrier' => 'max:255',
            'trans_number' => 'max:255',
            'last_country' => 'max:255',
            'dep_country' => 'max:255',
            'delivery_date' => 'nullable|date',
            'load_date' => 'nullable|date',
        ]);

        $orderItem = OrderItem::find($validatedData['itemId']);
        if (!$orderItem) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order item not found');
        }

        // Available quantity check
        if ($orderItem->is_taken_from_warehouse) {
            $availableItemQuantity = (float)$this->getItemFieldDataByType($validatedData['itemId'], ConfigDefaultInterface::FIELD_TYPE_AVAILABLE_AMOUNT_FROM_WAREHOUSE)?->value;
        } else {
            $availableItemQuantity = (float)$this->getItemFieldDataByType($validatedData['itemId'], ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE)?->value;
        }

        // Create a manual validator for the available quantity check
        $validator = Validator::make($validatedData, [
            'quantity' => [
                function ($attribute, $value, $fail) use ($availableItemQuantity) {
                    if ($value > $availableItemQuantity) {
                        $fail('The selected quantity exceeds the available stock');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            // Return with the validation errors
            return back()->withErrors($validator)->withInput();
        }

        $orderItem->buyers()->create([
            'name' => $validatedData['buyer'],
            'quantity' => $validatedData['quantity'],
            'address' => $validatedData['address'],
            'carrier' => $validatedData['carrier'],
            'trans_number' => $validatedData['trans_number'],
            'last_country' => $validatedData['last_country'],
            'dep_country' => $validatedData['dep_country'],
            'load_date' => $validatedData['load_date'],
            'delivery_date' => $validatedData['delivery_date'],
        ]);

        $this->executeItemCalculations($orderItem);


        if (!$orderItem->is_taken_from_warehouse) {
            WarehouseService::updateItemWarehouseStock($orderItem);
        }

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Item buyer was added '));
    }

    public function editBuyer(int $orderId, int $itemId, int $buyerId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order item not found');
        }

        if ($item->is_locked) {
            return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_ERROR, 'Item is locked and can not be modified');
        }

        $buyer = ItemBuyer::find($buyerId);
        if (!$buyer) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item buyer not found');
        }

        $isEdit = true;
        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $availableBuyers = ItemBuyer::query()->distinct()->pluck('name')->toArray();

        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];

        // Available quantity check
        if ($item->is_taken_from_warehouse) {
            $availableItemQuantity = (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_AVAILABLE_AMOUNT_FROM_WAREHOUSE)?->value;
            $availableItemQuantity += $buyer->quantity;
        } else {
            $availableItemQuantity = (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE)?->value;
            $availableItemQuantity += $buyer->quantity;
        }

        $countryMap = ConfigDefaultInterface::ORDER_COUNTRY_MAP;

        // Retrieve carriers
        $fieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_CARRIER)->id;
        $itemCarriers = $this->factory()->createOrderManager()->getDynamicSelectOptionsByField($fieldId, ItemsTableConfig::TABLE_NAME);
        $buyerCarriers = ItemBuyer::whereNotNull('carrier')->pluck('carrier')->unique()->toArray();
        $carriers = array_merge($itemCarriers, $buyerCarriers);
        $carriers = array_unique($carriers);

        return view(
            'main.user.order.add-buyer',
            compact('orderData', 'itemId', 'orderId', 'availableBuyers', 'isEdit', 'buyer', 'availableItemQuantity', 'excludedFieldsForDetails', 'carriers', 'countryMap'),
        );
    }

    public function updateBuyer(Request $request, int $orderId, int $itemId, int $buyerId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order item not found');
        }

        $buyer = ItemBuyer::find($buyerId);
        if (!$buyer) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item buyer not found');
        }

        // Validation rules
        $validatedData = $request->validate([
            'buyer' => 'required',
            'quantity' => 'required|numeric|min:1',
            'itemId' => 'required',
            'address' => 'max:255',
            'carrier' => 'max:255',
            'trans_number' => 'max:255',
            'last_country' => 'max:255',
            'dep_country' => 'max:255',
            'delivery_date' => 'nullable|date',
            'load_date' => 'nullable|date',
        ]);

        // Available quantity check
        if ($item->is_taken_from_warehouse) {
            $availableItemQuantity = (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_AVAILABLE_AMOUNT_FROM_WAREHOUSE)?->value;
            $availableItemQuantity += $buyer->quantity;
        } else {
            $availableItemQuantity = (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE)?->value;
            $availableItemQuantity += $buyer->quantity;
        }

        // Create a manual validator for the available quantity check
        $validator = Validator::make($validatedData, [
            'quantity' => [
                function ($attribute, $value, $fail) use ($availableItemQuantity) {
                    if ($value > $availableItemQuantity) {
                        $fail('The selected quantity exceeds the available stock');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            // Return with the validation errors
            return back()->withErrors($validator)->withInput();
        }

        $buyer->update([
            'name' => $validatedData['buyer'],
            'quantity' => $validatedData['quantity'],
            'address' => $validatedData['address'],
            'carrier' => $validatedData['carrier'],
            'trans_number' => $validatedData['trans_number'],
            'last_country' => $validatedData['last_country'],
            'dep_country' => $validatedData['dep_country'],
            'load_date' => $validatedData['load_date'],
            'delivery_date' => $validatedData['delivery_date'],
        ]);

        $this->executeItemCalculations($item);

        if (!$item->is_taken_from_warehouse) {
            WarehouseService::updateItemWarehouseStock($item);
        }

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Item buyer was updated '));
    }

    public function removeBuyer(int $orderId, int $itemId, int $buyerId)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_REMOVE_ITEM_BUYER)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $item = OrderItem::find($itemId);
        if (!$item) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order item not found');
        }

        if ($item->is_locked) {
            return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_ERROR, 'Item is locked and can not be modified');
        }

        $buyer = ItemBuyer::find($buyerId);
        if (!$buyer) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item buyer not found');
        }

        // Check if buyer has invoice
        $buyerInvoice = Invoice::where('order_id', $orderId)->where('customer', $buyer->name)->first();
        if ($buyerInvoice) {
            return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_ERROR, sprintf('Can not delete buyer that has invoice assigned'));
        }

        $buyer->delete();

        $this->executeItemCalculations($item);

        if (!$item->is_taken_from_warehouse) {
            WarehouseService::updateItemWarehouseStock($item);
        }

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Item buyer was removed '));
    }

    public function editCustomerInvoice(int $orderId, string $customer)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);

        $invoice = Invoice::where('order_id', $orderId)->where('customer', $customer)->first();
        $sumCalculations = InvoiceService::calculateSum($orderId, $customer);

        if ($invoice) {
            $invoiceData = [
                'is_new' => false,
                'number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'issue_date' => $invoice->issue_date,
                'pay_until_date' => $invoice->pay_until_date,
                'id' => $invoice->id,
                'calculated_sum' => $sumCalculations['calculated_sum'],
                'calculation_details' => $sumCalculations['details'],
                'sum' => number_format($invoice->sum, 2, '.', ''),
            ];
        } else {
            $invoiceData = [
                'is_new' => true,
                'number' => null,
                'status' => null,
                'issue_date' => null,
                'pay_until_date' => null,
                'calculated_sum' => $sumCalculations['calculated_sum'],
                'calculation_details' => $sumCalculations['details'],
                'sum' => null,
            ];
        }

        $excludedFieldsForDetails = [
            'from_warehouse' => TableService::getExcludedItemFields(1),
            'not_from_warehouse' => TableService::getExcludedItemFields(0),
        ];

        $invoiceStatusSelect = ConfigDefaultInterface::AVAILABLE_INVOICE_STATUS_SELECT;

        return view('main.user.order.edit-customer-invoice', compact('orderData', 'customer', 'invoiceData', 'invoiceStatusSelect', 'orderId', 'excludedFieldsForDetails'));
    }

    public function saveCustomerInvoice(Request $request, int $orderId, string $customer)
    {
        $customMessages = [
            'required' => 'Šis laukas yra privalomas.',
            'date' => 'Šis laukas nėra galiojanti data.',
            'in' => 'Pasirinkta reikšmė šiam laukui yra netinkama.',
            'unique' => 'Toks sąskaitos faktūros numeris jau egzistuoja. Prašome pasirinkti kitą numerį.',
        ];

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $invoiceId = $request->invoice_id;
        $invoiceEntity = $this->getInvoiceEntity($invoiceId);

        if ($invoiceId && $invoiceEntity) {
            // Update old invoice
            $validated = $request->validate([
                'invoice_number' => [
                    'required',
                    'string',
                    Rule::unique('invoices', 'invoice_number')->ignore($invoiceEntity->id),
                ],
                'invoice_issue_date' => 'date',
                'invoice_pay_until_date' => 'date',
                'sum' => 'required',
                'invoice_status' => [
                    'required',
                    'string',
                    'in:' . implode(',', array_keys(ConfigDefaultInterface::AVAILABLE_INVOICE_STATUS_SELECT))
                ],
            ], $customMessages);

            $invoiceEntity->update([
                'invoice_number' => $validated['invoice_number'],
                'issue_date' => $validated['invoice_issue_date'],
                'pay_until_date' => $validated['invoice_pay_until_date'],
                'status' => $validated['invoice_status'],
                'sum' => $validated['sum'],
            ]);

            $returnMessage = sprintf('Invoice for %s updated', $customer);
        } else {
            // Create new invoice
            $validated = $request->validate([
                'invoice_number' => 'required|string|unique:invoices,invoice_number',
                'invoice_issue_date' => 'date',
                'invoice_pay_until_date' => 'date',
                'sum' => 'required',
                'invoice_status' => [
                    'required',
                    'string',
                    'in:' . implode(',', array_keys(ConfigDefaultInterface::AVAILABLE_INVOICE_STATUS_SELECT))
                ],
            ], $customMessages);

            Invoice::create([
                'invoice_number' => $validated['invoice_number'],
                'issue_date' => $validated['invoice_issue_date'],
                'pay_until_date' => $validated['invoice_pay_until_date'],
                'status' => $validated['invoice_status'],
                'order_id' => $orderId,
                'customer' => $customer,
                'sum' => $validated['sum'],
            ]);

            $returnMessage = sprintf('Invoice for %s created', $customer);
        }

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, $returnMessage);
    }

    public function storeComment(Request $request, int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $validated = $request->validate([
            'content' => 'required|max:1000'
        ]);

        Comment::create([
            'user_id' => Auth::user()->id,
            'order_id' => $orderId,
            'content' => $validated['content'],
        ]);

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, 'New comment has been added');
    }
    public function deleteComment(Request $request, int $commentId)
    {
        $comment = Comment::find($commentId);
        if (!$comment) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Comment not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_DELETE_ORDER_COMMENTS)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $orderId = $comment->order_id;
        $comment->delete();

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, 'Comment has been deleted');
    }

    public function deleteInvoice(int $orderId, int $fieldId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_DELETE_INVOICE)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $orderInvoice = OrderData::where('order_id', $orderId)->where('field_id', $fieldId)->first();
        if ($orderInvoice === null) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Invoice not found');
        }

        $invoiceNumber = $orderInvoice->value;
        $invoiceEntity = Invoice::where('invoice_number', $invoiceNumber)->first();
        if ($invoiceEntity) {
            $invoiceEntity->delete();
        }

        $orderInvoice->delete();

        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(Auth::user()->email)
            ->setTitle(ActivityLogConstants::DANGER_LOG)
            ->setAction(ActivityLogConstants::ACTION_DELETE)
            ->setNewData(sprintf('invoice: %s', $invoiceNumber));

        $this->factory()->createActivityLogManager()->log($transfer);

        return redirect()->route('orders.view', ['id' => $orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, 'Invoice deleted');
    }

    protected function extractTargetItem(array $orderData, int $itemId): array
    {
        foreach ($orderData['items'] as $item) {
            if ($item['settings']['item_id'] == $itemId) {
                return $item['details'];
            }
        }

        return [];
    }

    protected function logItemAdded(Order $order, OrderItem $orderItem): void
    {
        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(Auth::user()->email)
            ->setTitle(ActivityLogConstants::INFO_LOG)
            ->setAction(ActivityLogConstants::ACTION_ADD)
            ->setNewData(sprintf('item %s for order: %s', $orderItem->getNameField(), $order->getKeyField()));

        $this->factory()->createActivityLogManager()->log($transfer);
    }

    protected function logItemRemoved(Order $order, OrderItem $orderItem): void
    {
        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(Auth::user()->email)
            ->setTitle(ActivityLogConstants::DANGER_LOG)
            ->setAction(ActivityLogConstants::ACTION_DELETE)
            ->setNewData(sprintf('item %s of order: %s', $orderItem->getNameField(), $order->getKeyField()));

        $this->factory()->createActivityLogManager()->log($transfer);
    }

    protected function logOrderCompanyChanged(Order $order, string $oldCompany, string $newCompany): void
    {
        $transfer = $this->factory()
            ->getActivityLogTransferObject()
            ->setUser(Auth::user()->email)
            ->setTitle(ActivityLogConstants::INFO_LOG)
            ->setAction(ActivityLogConstants::ACTION_UPDATE)
            ->setNewData(sprintf('Order %s company: %s to: %s', $order->getKeyField(), $oldCompany, $newCompany));

        $this->factory()->createActivityLogManager()->log($transfer);
    }

    protected function executeItemCalculations(OrderItem $orderItem): void
    {
        $calculator = $this->factory()->createOrderDataCalculator();

        $calculator->calculateTotalSalesAmount($orderItem);
        $calculator->calculatePurchaseSumForItem($orderItem);
        $calculator->calculateSalesSumForItem($orderItem);
        $calculator->calculateQuantityGoingToWarehouse($orderItem);
        $calculator->calculateAvailableQuantityFromWarehouse($orderItem);

        $this->executeOrderCalculations($orderItem->order);
    }

    protected function executeOrderCalculations(Order $order): void
    {
        $calculator = $this->factory()->createOrderDataCalculator();

        $calculator->calculateTotalPurchaseSum($order);
        $calculator->calculateDuty7($order);
        $calculator->calculateDuty15($order);
        $calculator->calculatePrimeCost($order);
        $calculator->calculateTotalSalesSum($order);
        $calculator->calculateTotalProfit($order);
        $calculator->calculateOtherCosts($order);
    }

    protected function executeInvoiceUpdate(Request $request, array|string|null $fieldId, int $updatedFields, int $orderId, ?string $value): int
    {
        $allInputs = $request->all();
        $invoiceNumber = 'field_' . $fieldId;
        $issueDate = 'invoice_issue_date';
        $payUntilDate = 'invoice_pay_until_date';
        $status = 'invoice_status';
        $id = 'invoice_id';
        $sum = 'sum';

        $customMessages = [
            'required' => 'Šis laukas yra privalomas.',
            'date' => 'Šis laukas nėra galiojanti data.',
            'in' => 'Pasirinkta reikšmė šiam laukui yra netinkama.',
            'unique' => 'Toks sąskaitos faktūros numeris jau egzistuoja. Prašome pasirinkti kitą numerį.',
        ];

        // If invoice is provided then update
        $invoiceEntity = $this->getInvoiceEntity($allInputs[$id]);
        if ($value && $invoiceEntity) {
            $validated = $request->validate([
                $invoiceNumber => [
                    'required',
                    'string',
                    Rule::unique('invoices', 'invoice_number')->ignore($invoiceEntity->id),
                ],
                $issueDate => 'date',
                $payUntilDate => 'date',
                $sum => 'required',
                $status => [
                    'required',
                    'string',
                    'in:' . implode(',', array_keys(ConfigDefaultInterface::AVAILABLE_INVOICE_STATUS_SELECT))
                ],
            ], $customMessages);

            $invoiceEntity->update([
                'invoice_number' => $validated[$invoiceNumber],
                'issue_date' => $validated[$issueDate],
                'pay_until_date' => $validated[$payUntilDate],
                'status' => $validated[$status],
                'sum' => $validated[$sum],
            ]);
        } else {
            $validated = $request->validate([
                $invoiceNumber => 'required|string|unique:invoices,invoice_number',
                $issueDate => 'date',
                $payUntilDate => 'date',
                $sum => 'required',
                $status => [
                    'required',
                    'string',
                    'in:' . implode(',', array_keys(ConfigDefaultInterface::AVAILABLE_INVOICE_STATUS_SELECT))
                ],
            ], $customMessages);

            Invoice::create([
                'invoice_number' => $validated[$invoiceNumber],
                'issue_date' => $validated[$issueDate],
                'pay_until_date' => $validated[$payUntilDate],
                'status' => $validated[$status],
                'sum' => $validated[$sum],
                'order_id' => $orderId,
                'field_id' => $fieldId,
            ]);
        }

        return $updatedFields + 1;
    }

    protected function getInvoiceEntity(?string $id = null): ?Invoice
    {
        if (!$id) {
            return null;
        }

        return Invoice::find($id);
    }

    protected function getItemFieldDataByType(int $itemId, string $targetField): ?OrderItemData
    {
        $targetFieldId = TableField::where('type', $targetField)->first()?->id;

        return OrderItemData::where('order_item_id', $itemId)->where('field_id', $targetFieldId)->first();
    }
}
