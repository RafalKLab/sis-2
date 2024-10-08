<?php

namespace App\Business\Table\Reader;

use App\Business\Table\Config\TableConfig;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Table\Table;
use App\Models\Table\TableField;
use Illuminate\Support\Facades\Auth;

class AdminTableReader implements TableReaderInterface
{
    protected const PAGINATION_ITEMS_PER_PAGE = 10;

    protected bool $failedSearch = false;
    protected ?int $exactMatch = null;

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
            'exact_match' => $this->exactMatch
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

    public function findMainTable(): ?Table
    {
        return Table::where('name', TableConfig::MAIN_TABLE_NAME)->first();
    }

    protected function findOrders(Table $mainTable, ?string $search = null): array
    {
        $searchedOrderIds = $this->findSearchedOrders($search);
        if ($searchedOrderIds) {
            $orders = Order::whereIn('id', $searchedOrderIds)
                ->orderBy('updated_at', 'asc')
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

    /**
     * not used anymore for admin tab
     *
     * @param string|null $search
     *
     * @return array
     */
    protected function findSearchedOrders(?string $search): array
    {
        $orderKeyFieldId = null;

        $userFields = Auth::user()
            ->getAssignedFields()
            ->pluck('id', 'type')
            ->toArray();

        // Check if user can see order key field
        if (array_key_exists('id', $userFields)) {
            $orderKeyFieldId = $userFields['id'];
        }

        if ($orderKeyFieldId) {
            // First, attempt to find an exact match for the order ID
            $exactMatch = OrderData::where('field_id', $orderKeyFieldId)
                ->where('value', $search)
                ->pluck('order_id')
                ->toArray();

            // If an exact match is found, return it
            if (!empty($exactMatch)) {
                $this->exactMatch = $exactMatch[0];

                return $exactMatch;
            }
        }

        // Perform the broader search if no exact match was found
        $foundIds = OrderData::where('value', 'like', '%' . $search . '%')
            ->whereIn('field_id', $userFields)
            ->pluck('order_id')
            ->toArray();

        if (!$foundIds) {
            $this->failedSearch = true;

            return [];
        }

        // Remove duplicate IDs from the array
        return  array_unique($foundIds);
    }
}
