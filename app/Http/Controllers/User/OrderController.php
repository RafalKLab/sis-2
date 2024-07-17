<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use Illuminate\Http\Request;
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

        $orderData = $this->factory()->createOrderManager()->getOrderDetails($order);

        return view('main.user.order.show', compact('orderData'));
    }

    public function edit(int $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
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
                        'value' => $value
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

            return redirect()->route('orders.view', ['id'=>$orderId])->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Successfully updated %s fields', $updatedFields));
        } else {
            return redirect()->route('orders.view', ['id'=>$orderId]);
        }
    }
}
