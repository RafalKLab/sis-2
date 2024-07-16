<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Models\Order\Order;
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
}
