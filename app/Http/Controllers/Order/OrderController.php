<?php

namespace App\Http\Controllers\Order;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Http\Controllers\MainController;
use App\Models\Order\Invoice;
use App\Models\Order\ItemBuyer;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\TableField;
use App\Service\OrderService;
use App\Service\TableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use shared\ConfigDefaultInterface;

class OrderController extends MainController
{
    public function index(Request $request)
    {
        $search = $request->search;
        $tableData = $this->factory()->createTableManager()->retrieveTableData($search);
        if ($tableData['exact_match']) {
            return redirect()->route('orders.view', ['id'=>$tableData['exact_match']]);
        }

        return view('main.user.order.index', compact('tableData', 'search'));
    }

    public function view(int $orderId)
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

        return view('main.user.order.show', compact('orderData'));
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

        return view('main.user.order.edit', compact('orderData', 'orderFormData'));
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

        return view('main.user.order.edit', compact('orderData', 'orderFormData'));
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

    public function register()
    {
        $user = auth()->user();
        if (!($user->hasRole(ConfigDefaultInterface::ROLE_ADMIN) || $user->hasPermissionTo(ConfigDefaultInterface::PERMISSION_REGISTER_ORDER))) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User does not have permission to register new order');
        }

        $orderKeyFieldId = OrderService::getKeyField()->id;
        $relatedOrders = OrderData::where('field_id', $orderKeyFieldId)->orderBy('value', 'desc')->pluck('value', 'order_id')->toArray();

        return view('main.user.order.register', compact('relatedOrders'));
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
        ]);

        $parentOrder = Order::find($validated['related_order']);

        $newOrder = Order::create([
            'parent_id' => $parentOrder?->id,
            'user_id' => auth()->user()->id,
        ]);

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

        return view('main.user.order.add-item', compact('orderData', 'orderFormData'));
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

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('New item was added to order'));
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

        $isEdit = true;
        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $orderFormData = $this->extractTargetItem($orderData, $itemId);

        return view('main.user.order.edit-item', compact('orderData', 'orderFormData', 'isEdit', 'itemId'));
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

        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
            if ($order->user_id !== Auth::user()->id) {
                return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
            }
        }

        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $availableBuyers = ItemBuyer::query()->distinct()->pluck('name')->toArray();

        return view('main.user.order.add-buyer', compact('orderData', 'itemId', 'orderId', 'availableBuyers'));
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

        // TODO: Implement balance check

        // Validation rules
        $validatedData = $request->validate([
            'buyer' => 'required',
            'quantity' => 'required|numeric|min:1', // Ensure quantity is a number and at least 1
            'itemId' => 'required',
        ]);

        $orderItem = OrderItem::find($validatedData['itemId']);
        if (!$orderItem) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order item not found');
        }
        $orderItem->buyers()->create([
            'name' => $validatedData['buyer'],
            'quantity' => $validatedData['quantity'],
        ]);

        $this->executeItemCalculations($orderItem);

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

        $buyer = ItemBuyer::find($buyerId);
        if (!$buyer) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item buyer not found');
        }

        $isEdit = true;
        $orderData = $this->factory()->createOrderManager()->getOrderDetailsWithGroups($order);
        $availableBuyers = ItemBuyer::query()->distinct()->pluck('name')->toArray();

        return view('main.user.order.add-buyer', compact('orderData', 'itemId', 'orderId', 'availableBuyers', 'isEdit', 'buyer'));
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

        // TODO: Implement balance check

        // Validation rules
        $validatedData = $request->validate([
            'buyer' => 'required',
            'quantity' => 'required|numeric|min:1', // Ensure quantity is a number and at least 1
            'itemId' => 'required',
        ]);

        $buyer->update([
            'name' => $validatedData['buyer'],
            'quantity' => $validatedData['quantity'],
        ]);

        $this->executeItemCalculations($item);

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

        $buyer = ItemBuyer::find($buyerId);
        if (!$buyer) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Item buyer not found');
        }

        // TODO Implement permissions and check invoice
        $buyer->delete();

        $this->executeItemCalculations($item);

        return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Item buyer was removed '));
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

    protected function executeItemCalculations(OrderItem $orderItem): void
    {
        $calculator = $this->factory()->createOrderDataCalculator();

        $calculator->calculateTotalSalesAmount($orderItem);
        $calculator->calculatePurchaseSumForItem($orderItem);
        $calculator->calculateSalesSumForItem($orderItem);

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
    }

    protected function executeInvoiceUpdate(Request $request, array|string|null $fieldId, int $updatedFields, int $orderId, ?string $value): int
    {
        $allInputs = $request->all();
        $invoiceNumber = 'field_' . $fieldId;
        $issueDate = 'invoice_issue_date';
        $payUntilDate = 'invoice_pay_until_date';
        $status = 'invoice_status';
        $id = 'invoice_id';

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
            ]);
        } else {
            $validated = $request->validate([
                $invoiceNumber => 'required|string|unique:invoices,invoice_number',
                $issueDate => 'date',
                $payUntilDate => 'date',
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
}
