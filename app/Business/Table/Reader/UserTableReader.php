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

    protected const PAGINATION_ITEMS_PER_PAGE = 50;

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
                    ->orderBy('score', 'desc')
                    ->paginate(self::PAGINATION_ITEMS_PER_PAGE);
            } else {
                $orders = Order::where('user_id', Auth::user()->id)
                    ->whereIn('id', $searchedOrderIds)
                    ->orderBy('score', 'desc')
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

        $ordersData = $this->flattenOrders($ordersData);

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
            'data.field',
            'user',
            'files',
            'company',
            'comments'
        ]);

        $fieldsById = $this->userFields->keyBy('id');

        $ordersData = $orders->map(function ($order) use ($fieldsById, $fileFieldName) {
            $data = [
                'id' => $order->id,
                'user' => $order->user->name,
                'company' => $order->company?->name,
                'uploaded_files' => $order->files->count(),
                'parent_order' => $order->parent_id,
                $fileFieldName => '',
                'comment' => $order->comments->first()?->content,
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

//                if ($field['type'] == ConfigDefaultInterface::FIELD_ITEM_LOAD_DATE && $value) {
//                    $value = sprintf('<div class="order-field-status-yellow">%s</div>', $value);
//                }
//
//                if ($field['type'] == ConfigDefaultInterface::FIELD_ITEM_DELIVERY_DATE && $value) {
//                    $value = sprintf('<div class="order-field-status-brown">%s</div>', $value);
//                }
//
//                if ($field['type'] == ConfigDefaultInterface::FIELD_ITEM_LOAD_DATE_FROM_WAREHOUSE && $value) {
//                    $value = sprintf('<div class="order-field-status-green">%s</div>', $value);
//                }
//
//                if ($field['type'] == ConfigDefaultInterface::FIELD_ITEM_DELIVERY_DATE_TO_BUYER && $value) {
//                    $value = sprintf('<div class="order-field-status-purple">%s</div>', $value);
//                }

                $itemData[] = $value;
            }

            $buyerDetails = [
                'load_date' => '',
                'load_country' => '',
                'delivery_date' => '',
                'delivery_country' => '',
            ];

            $data[] = [
                'details' => $itemData,
                'buyers' => $this->collectOrderItemBuyers($item->id, $buyerDetails),
                'buyer_details' => $buyerDetails,
            ];
        }

        return $data;
    }

    private function collectOrderItemBuyers(int $id, array &$buyerDetails): string
    {
        $buyerNames = [];
        foreach (ItemBuyer::where('order_item_id', $id)->get() as $buyer) {
            $buyerNames[] = $buyer->name;
            $buyerDetails = [
                'load_date' => $buyer->load_date,
                'load_country' => $buyer->last_country,
                'delivery_date' => $buyer->delivery_date,
                'delivery_country' => $buyer->dep_country,
            ];
        }

        return implode(', ', $buyerNames);
    }

    private function flattenOrders(array $ordersData): array
    {
        $mainOrders = [];
        $childrenOrders = [];
        foreach ($ordersData as $order) {
            if ($order['parent_order'] === null) {
                $mainOrders[] = $order;
            } else {
                $childrenOrders[$order['parent_order']][] = $order;
            }
        }

        // Reverse each set of child orders
        foreach ($childrenOrders as $parentOrderId => $childOrders) {
            $childrenOrders[$parentOrderId] = array_reverse($childOrders);
        }

        $ordersWithChildren = [];
        foreach ($mainOrders as $order) {
            $ordersWithChildren[] = $order;
            $ordersWithChildren = array_merge($ordersWithChildren, $this->flattenOrdersWithChildren($order['id'], $childrenOrders));
        }

        if (empty($ordersWithChildren)) {
            return $ordersData;
        }

        return $ordersWithChildren;
    }

    function flattenOrdersWithChildren($orderId, &$childrenOrders): array
    {
        $flattenedOrders = [];

        if (array_key_exists($orderId, $childrenOrders)) {
            foreach ($childrenOrders[$orderId] as $childOrder) {
                // Add the child order
                $flattenedOrders[] = $childOrder;
                $flattenedOrders = array_merge($flattenedOrders, $this->flattenOrdersWithChildren($childOrder['id'], $childrenOrders));
            }
        }

        return $flattenedOrders;
    }
}
