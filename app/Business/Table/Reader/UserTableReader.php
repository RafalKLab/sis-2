<?php

namespace App\Business\Table\Reader;

use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Table\Table;
use Illuminate\Support\Facades\Auth;

class UserTableReader extends AdminTableReader
{
    /**
     * override show only assigned fields
     *
     * @param Table|null $mainTable
     *
     * @return array
     */
    public function readTableFields(?Table $mainTable = null): array
    {
        $fieldData = [];
        foreach (Auth::user()->fields as $field) {
            $fieldData[] = [
                'id' => $field->id,
                'name' => $field->name,
                'type' => $field->type,
                'color' => $field->color,
                'order' => $field->order
            ];
        }

        return $fieldData;
    }

    /**
     * override show only assigned fields
     *
     * @param Table $mainTable
     * @param string|null $search
     *
     * @return array
     */
    protected function findOrders(Table $mainTable, ?string $search = null): array
    {
        //TODO: Adjust search
        $searchedOrderIds = $this->findSearchedOrders($search);
        if ($searchedOrderIds) {
            $orders = Order::whereIn('id', $searchedOrderIds)
                ->orderBy('updated_at', 'desc')
                ->paginate(self::PAGINATION_ITEMS_PER_PAGE);
        } else {
            if ($this->failedSearch) {
                return [
                    'data' => [],
                    'links' => '',
                ];
            }

            $orders = Order::orderBy('updated_at', 'desc')->paginate(self::PAGINATION_ITEMS_PER_PAGE);
        }

        $ordersData = [];

        foreach ($orders as $order) {
            $data = [];

            $data['id'] = $order->id;
            foreach (Auth::user()->fields as $field) {
                $data[$field->name] = OrderData::where('order_id', $order->id)->where('field_id', $field->id)->first()?->value;
            }

            $ordersData[] = $data;
        }

        return [
            'data' => $ordersData,
            'links' => $orders->links()
        ];
    }
}
