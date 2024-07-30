<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\MainController;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
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

        $orderData = $this->factory()->createOrderManager()->getOrderDetails($order);

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

        $orderData = $this->factory()->createOrderManager()->getOrderDetails($order);
        $orderFormData = $orderData;

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

        $orderData = $this->factory()->createOrderManager()->getOrderDetails($order);
        $orderFormData = $orderData;

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
        foreach ($fieldInputs as $key => $value)
        {
            // Remove 'field_' to get just the numeric ID and find order data for that field
            $fieldId = preg_replace('/[^0-9]/', '', $key);
            $orderData = OrderData::where('order_id', $orderId)->where('field_id', $fieldId)->first();

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
            $this->calculatePurchaseSum($order);
            $this->calculateDuty7($order);
            $this->calculateDuty15($order);
            $this->calculatePrimeCost($order);
            $this->calculateSalesSum($order);

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

    protected function calculatePurchaseSum(Order $order): void
    {
        $purchaseNumber = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_NUMBER)?->value;
        if (!$purchaseNumber) {
            return;
        }

        $amount = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_AMOUNT)?->value;
        if (!$amount) {
            return;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseNumber;
        $secondNumber = (float) $amount;

        $result = ($firstNumber * $secondNumber);
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)->id,
            ];

            $order->data()->create($data);
        }
    }

    protected function calculateDuty7(Order $order): void
    {
        // 1 collect needed fields purchase number and amount
        $purchaseSum = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)?->value;
        if (!$purchaseSum) {
            return;
        }

        $transportPrice1 = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1)?->value;
        if (!$transportPrice1) {
            return;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseSum;
        $secondNumber = (float) $transportPrice1;

        $result = ($firstNumber + $secondNumber) * 0.07;
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_DUTY_7);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_DUTY_7)->id,
            ];

            $order->data()->create($data);
        }
    }

    protected function calculateDuty15(Order $order): void
    {
        // 1 collect needed fields purchase number and amount
        $purchaseSum = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)?->value;
        if (!$purchaseSum) {
            return;
        }

        $transportPrice1 = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1)?->value;
        if (!$transportPrice1) {
            return;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseSum;
        $secondNumber = (float) $transportPrice1;

        $result = ($firstNumber + $secondNumber) * 0.158;
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_DUTY_15);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_DUTY_15)->id,
            ];

            $order->data()->create($data);
        }
    }

    protected function calculatePrimeCost(Order $order): void
    {
        $primeCostComponents = [
            ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM,
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1,
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_2,
            ConfigDefaultInterface::FIELD_TYPE_DUTY_7,
            ConfigDefaultInterface::FIELD_TYPE_DUTY_15,
            ConfigDefaultInterface::FIELD_TYPE_BROKER,
            ConfigDefaultInterface::FIELD_TYPE_WAREHOUSES,
            ConfigDefaultInterface::FIELD_TYPE_BANK,
            ConfigDefaultInterface::FIELD_TYPE_OTHER_COSTS,
            ConfigDefaultInterface::FIELD_TYPE_FLAW,
            ConfigDefaultInterface::FIELD_TYPE_AGENT,
            ConfigDefaultInterface::FIELD_TYPE_FACTORING,
        ];
        $primeCost = 0.00;

        foreach ($primeCostComponents as $costField) {
            $value = $this->getOrderFieldData($order, $costField)?->value;
            if (!$value) {
                continue;
            }

            $primeCost += (float) $value;
        }

        $primeCostFormatted = number_format($primeCost, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PRIME_COST);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $primeCostFormatted,
            ]);
        } else {
            $data = [
                'value' => $primeCostFormatted,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PRIME_COST)->id,
            ];

            $order->data()->create($data);
        }
    }

    protected function calculateSalesSum(Order $order): void {
        $salesNumber = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_SALES_NUMBER)?->value;
        if (!$salesNumber) {
            return;
        }

        $amount = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_AMOUNT)?->value;
        if (!$amount) {
            return;
        }

        // Convert strings to floats
        $firstNumber = (float) $salesNumber;
        $secondNumber = (float) $amount;

        $result = ($firstNumber * $secondNumber);
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_SALES_SUM);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SALES_SUM)->id,
            ];

            $order->data()->create($data);
        }
    }

    protected function getOrderFieldData(Order $order, string $targetField): ?OrderData
    {
        $targetFieldId = TableField::where('type', $targetField)->first()?->id;

        return OrderData::where('order_id', $order->id)->where('field_id', $targetFieldId)->first();
    }
}