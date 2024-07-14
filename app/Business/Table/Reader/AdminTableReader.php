<?php

namespace App\Business\Table\Reader;

use App\Business\Table\Config\TableConfig;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Table\Table;
use App\Models\Table\TableField;

class AdminTableReader implements TableReaderInterface
{
    protected const PAGINATION_ITEMS_PER_PAGE = 15;

    public function readTableData(?string $search): array
    {
        $mainTable = $this->findMainTable();
        if (!$mainTable) {
            return [];
        }

        $fieldData = $this->readTableFields($mainTable);
        $orders = $this->findOrders($mainTable, $search);

        return [
            'name' => $mainTable->name,
            'fields' => $fieldData,
            'orders' => $orders,
        ];
    }

    public function readTableFields(?Table $mainTable = null): array
    {
        if (!$mainTable) {
            $mainTable = $this->findMainTable();
        }

        $fieldData = [];
        foreach ($mainTable->fields as $field) {
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

    public function getTableField(int $id): ?TableField
    {
        return TableField::find($id);
    }

    private function findMainTable(): ?Table
    {
        return Table::where('name', TableConfig::MAIN_TABLE_NAME)->first();
    }

    private function findOrders(Table $mainTable, ?string $search = null): array
    {
        $searchedOrderIds = $this->findSearchedOrders($search);
        if ($searchedOrderIds) {
            $orders = Order::whereIn('id', $searchedOrderIds)
                ->orderBy('updated_at', 'desc')
                ->paginate(self::PAGINATION_ITEMS_PER_PAGE);
        } else {
            $orders = Order::orderBy('updated_at', 'desc')->paginate(self::PAGINATION_ITEMS_PER_PAGE);
        }

        $ordersData = [];

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

    private function findSearchedOrders(?string $search): array
    {
        $foundIds = OrderData::where('value', 'like', '%' . $search . '%')
            ->pluck('order_id')
            ->toArray();

        if (!$foundIds) {
            return [];
        }

        // Remove duplicate IDs from the array
        return  array_unique($foundIds);
    }
}
