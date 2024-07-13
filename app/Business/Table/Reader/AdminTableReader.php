<?php

namespace App\Business\Table\Reader;

use App\Business\Table\Config\TableConfig;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Table\Table;

class AdminTableReader implements TableReaderInterface
{
    protected const PAGINATION_ITEMS_PER_PAGE = 20;

    public function readTableData(): array
    {
        $mainTable = $this->findMainTable();
        if (!$mainTable) {
            return [];
        }

        $fieldData = $this->findTableFields($mainTable);
        $orders = $this->findOrders($mainTable);

        return [
            'name' => $mainTable->name,
            'fields' => $fieldData,
            'orders' => $orders,
        ];
    }

    private function findMainTable(): ?Table
    {
        return Table::where('name', TableConfig::MAIN_TABLE_NAME)->first();
    }

    private function findTableFields(Table $mainTable): array
    {
        $fieldData =[];
        foreach ($mainTable->fields as $field) {
            $fieldData[] = [
                'name' => $field->name,
                'type' => $field->type,
                'color' => $field->color,
            ];
        }

        return $fieldData;
    }

    private function findOrders(Table $mainTable): array
    {
        $ordersData = [];
        $orders = Order::paginate(self::PAGINATION_ITEMS_PER_PAGE);

        foreach ($orders as $order) {
            $data = [];

            $data['id'] = $order->id;
            foreach ($mainTable->fields as $field) {
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
