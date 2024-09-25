<?php

namespace App\Business\Table\Reader;

use App\Business\Table\Config\TableConfig;
use App\Models\Order\Invoice;
use App\Models\Order\ItemBuyer;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\Table;
use App\Models\Table\TableField;
use App\Models\Warehouse\Warehouse;
use App\Service\InvoiceService;
use App\Service\TableService;
use Illuminate\Support\Facades\Auth;
use shared\ConfigDefaultInterface;

class UserTableReader implements TableReaderInterface
{
    protected $userFields;

    protected const PAGINATION_ITEMS_PER_PAGE = 15;

    protected bool $failedSearch = false;
    protected ?int $exactMatch = null;

    public function __construct()
    {
        $this->userFields = Auth::user()->getAssignedFields();
    }

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

    /**
     * override show only assigned fields
     *
     * @param Table|null $mainTable
     *
     * @return array
     */
    public function readTableFields(?Table $mainTable = null): array
    {
        return $this->userFields->map(function ($field) {
            return [
                'id'    => $field->id,
                'name'  => $field->name,
                'type'  => $field->type,
                'color' => $field->color,
                'order' => $field->order,
            ];
        })->all();
    }

    public function getTableField(int $id): ?TableField
    {
        return TableField::find($id);
    }

    public function findMainTable(): ?Table
    {
        return Table::where('name', TableConfig::MAIN_TABLE_NAME)->first();
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
        $searchedOrderIds = $this->findSearchedOrders($search);
        if ($searchedOrderIds) {
            if (Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_ALL_ORDERS)) {
                $orders = Order::whereIn('id', $searchedOrderIds)
                    ->orderBy('updated_at', 'desc')
                    ->paginate(self::PAGINATION_ITEMS_PER_PAGE);
            } else {
                $orders = Order::where('user_id', Auth::user()->id)
                    ->whereIn('id', $searchedOrderIds)
                    ->orderBy('created_at', 'desc')
                    ->paginate(self::PAGINATION_ITEMS_PER_PAGE);
            }
        } else {
            if ($this->failedSearch) {
                return [
                    'data' => [],
                    'links' => '',
                ];
            }
        }

        $ordersData = $this->formatOrdersData($orders);

        return [
            'data' => $ordersData,
            'links' => $orders->links()
        ];
    }

    protected function findSearchedOrders(?string $search): array
    {
        $orderKeyFieldId = null;

        $userFields = $this->userFields
            ->pluck('id', 'type')
            ->toArray();

        //TODO: Rework, tmp exact match is turned off
//        // Check if user can see order key field
//        if (array_key_exists('id', $userFields)) {
//            $orderKeyFieldId = $userFields['id'];
//        }
//
//        if ($orderKeyFieldId) {
//            // First, attempt to find an exact match for the order ID
//            $exactMatch = OrderData::where('field_id', $orderKeyFieldId)
//                ->where('value', $search)
//                ->pluck('order_id')
//                ->toArray();
//
//            // If an exact match is found, return it
//            if (!empty($exactMatch)) {
//                $this->exactMatch = $exactMatch[0];
//
//                return $exactMatch;
//            }
//        }

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

    protected function formatOrdersData($orders): array
    {
        // Get item fields
        $itemFields = TableService::getItemFields();

        // Assuming $orders is a collection of Order models
        $fileFieldName = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_FILE)->name;

        $orders->load([
            'data.field', // Corrected relationship name
            'user',
            'files'
        ]);

        $fieldsById = $this->userFields->keyBy('id');

        $ordersData = $orders->map(function ($order) use ($fieldsById, $fileFieldName) {
            $data = [
                'id' => $order->id,
                'user' => $order->user->name,
                'uploaded_files' => $order->files->count(),
                $fileFieldName => '',
            ];

            // Using the correct relationship name 'data' instead of 'orderDataEntities'
            foreach ($order->data as $orderDataEntity) {
                // Only process if the field is part of the userFields
                if (isset($fieldsById[$orderDataEntity->field_id])) {
                    $targetField = $fieldsById[$orderDataEntity->field_id];
                    $data[$targetField->name] = $orderDataEntity->value;
                    $data['config'][$targetField->name] = [
                        'status_color_class' => $this->getStatusColorClass($targetField, $orderDataEntity),
                    ];
                }
            }

            return $data;
        });

        $data = $ordersData->all();


        // populate with order items data
        foreach ($data as &$order) {
            $order['items']['fields'] = $itemFields;
            $order['items']['data'] = $this->collectOrderItemsData($order['id'], $itemFields);
        }
        unset($order);

        return $data;
    }

    protected function getStatusColorClass(TableField $field, ?OrderData $orderDataEntity): string
    {
        $colorClass = match ($field->type) {
            'select status' => $this->getStatusColorClassForSelectStatusField($orderDataEntity),
            'invoice' => InvoiceService::getInvoiceDisplayColor($orderDataEntity?->value),
            default => '',
        };

        return $colorClass;
    }

    protected function getStatusColorClassForSelectStatusField(?OrderData $orderDataEntity): string
    {
        $status = $orderDataEntity?->value;

        if (!$status) {
            return '';
        }

        if (!array_key_exists($status, ConfigDefaultInterface::ORDER_STATUS_MAP)) {
            return '';
        }

        return ConfigDefaultInterface::ORDER_STATUS_MAP[$status];
    }

    private function collectOrderItemsData(int $id, array $itemFields): array
    {
        $warehouses = Warehouse::all()->pluck('name', 'id')->toArray();

        $data = [];
        $items = OrderItem::where('order_id', $id)->get();
        foreach ($items as $item) {
            $itemData = [];
            foreach ($itemFields as $field) {
                $value = OrderItemData::where('order_item_id', $item->id)->where('field_id', $field['id'])->first()?->value;

                if ($field['type'] === ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE) {
                    $value = $warehouses[$value] ?? null;
                }

                if ($field['type'] == ConfigDefaultInterface::FIELD_ITEM_LOAD_DATE && $value) {
                    $value = sprintf('<div class="order-field-status-yellow">%s</div>', $value);
                }

                if ($field['type'] == ConfigDefaultInterface::FIELD_ITEM_DELIVERY_DATE && $value) {
                    $value = sprintf('<div class="order-field-status-green">%s</div>', $value);
                }

                $itemData[] = $value;
            }

            $data[] = [
                'details' => $itemData,
                'buyers' => $this->collectOrderItemBuyers($item->id),
            ];
        }

        return $data;
    }

    private function collectOrderItemBuyers(int $id): string
    {
        $buyerNames = [];
        foreach (ItemBuyer::where('order_item_id', $id)->get() as $buyer) {
            $buyerNames[] = $buyer->name;
        }

        return implode(', ', $buyerNames);
    }
}
