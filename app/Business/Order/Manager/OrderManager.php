<?php

namespace App\Business\Order\Manager;

use App\Business\Table\Config\ItemsTableConfig;
use App\Business\Table\Config\TableConfig;
use App\Models\Company\Company;
use App\Models\Order\Invoice;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\TableField;
use App\Models\Warehouse\Warehouse;
use App\Service\InvoiceService;
use App\Service\OrderItemService;
use App\Service\TableService;
use DateTime;
use Illuminate\Support\Facades\Auth;
use shared\ConfigDefaultInterface;

class OrderManager
{
    public function getOrderDetails(Order $order): array
    {
        $orderData = [
            'id' => $order->id,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'uploaded_files' => $order->files()->count(),
            'user' => $order->user->name,
        ];


        foreach (Auth::user()->getAssignedFields() as $field) {
            $orderDataEntity = OrderData::where('order_id', $order->id)->where('field_id', $field->id)->first();

            if ($orderDataEntity) {
                if ($field->type === 'id') {
                    $orderData['key'] = $orderDataEntity->value;
                }

                $orderData['details'][] = [
                    'order_data_id' => $orderDataEntity->id,
                    'value' => $orderDataEntity->value,
                    'field_id' => $field->id,
                    'field_name' => $field->name,
                    'field_type' => $field->type,
                    'field_order' => $field->order,
                    'updated_by' => $orderDataEntity->lastUpdatedBy?->name,
                    'updated_at' => $orderDataEntity->updated_at,
                    'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, TableConfig::MAIN_TABLE_NAME),
                    'additional_data' => $this->getFieldAdditionalData($field, $order->id, $orderDataEntity),
                ];
            } else {
                $orderData['details'][] = [
                    'order_data_id' => '',
                    'value' => '',
                    'field_id' => $field->id,
                    'field_name' => $field->name,
                    'field_type' => $field->type,
                    'field_order' => $field->order,
                    'updated_by' => null,
                    'updated_at' => null,
                    'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, TableConfig::MAIN_TABLE_NAME),
                    'additional_data' => $this->getFieldAdditionalData($field, $order->id),
                ];
            }
        }

        return $orderData;
    }

    public function getOrderDetailsWithGroups(Order $order): array
    {
        $orderData = [
            'id' => $order->id,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'uploaded_files' => $order->files()->count(),
            'user' => $order->user->name,
            'items' => $this->getOrderItems($order),
            'company' => [
                'id' => $order->company?->id,
                'name' => $order->company?->name,
            ],
            'available_companies' => Company::all()->pluck('name', 'id')->toArray(),
            'related_order_parent_links' => $this->getRelatedOrderParentLinks($order),
            'related_order_children_links' => $this->getRelatedOrderChildrenLinks($order),
            'comments' => $this->retrieveOrderComments($order)
        ];

        foreach (Auth::user()->getAssignedFields() as $field) {
            $orderDataEntity = OrderData::where('order_id', $order->id)->where('field_id', $field->id)->first();

            if ($orderDataEntity) {
                if ($field->type === 'id') {
                    $orderData['key'] = $orderDataEntity->value;
                }

                $orderData['details'][$field->group][] = [
                    'order_data_id' => $orderDataEntity->id,
                    'value' => $orderDataEntity->value,
                    'field_id' => $field->id,
                    'field_name' => $field->name,
                    'field_type' => $field->type,
                    'field_order' => $field->order,
                    'updated_by' => $orderDataEntity->lastUpdatedBy?->name,
                    'updated_at' => $orderDataEntity->updated_at,
                    'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, TableConfig::MAIN_TABLE_NAME),
                    'additional_data' => $this->getFieldAdditionalData($field, $order->id, $orderDataEntity),
                ];
            } else {
                $orderData['details'][$field->group][] = [
                    'order_data_id' => '',
                    'value' => '',
                    'field_id' => $field->id,
                    'field_name' => $field->name,
                    'field_type' => $field->type,
                    'field_order' => $field->order,
                    'updated_by' => null,
                    'updated_at' => null,
                    'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, TableConfig::MAIN_TABLE_NAME),
                    'additional_data' => $this->getFieldAdditionalData($field, $order->id),
                ];
            }
        }

        // Add invoices for each buyer/customer
        $orderData['details']['PIRKĖJŲ SĄSKAITOS'] = $this->collectBuyerInvoices($order);

        return $orderData;
    }

    public function getItemFormData(): array
    {
        $data = [];

        foreach (TableService::getItemsTable()->fields as $field) {
            $data[] = [
                'order_data_id' => '',
                'value' => null,
                'field_id' => $field->id,
                'field_name' => $field->name,
                'field_type' => $field->type,
                'field_order' => $field->order,
                'updated_at' => null,
                'updated_by' => null,
                'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, ItemsTableConfig::TABLE_NAME),
            ];
        }

        return $data;
    }

    protected function getInputSelectByFieldType(string $type, int $fieldId, string $tableScope): array
    {
        $inputSelect = match($type) {
            ConfigDefaultInterface::FIELD_TYPE_SELECT_STATUS => ConfigDefaultInterface::ORDER_STATUS_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_GLUE => ConfigDefaultInterface::ORDER_GLUE_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_MEASUREMENT => ConfigDefaultInterface::ORDER_MEASUREMENT_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_CERTIFICATION => ConfigDefaultInterface::ORDER_CERTIFICATION_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_COUNTRY => ConfigDefaultInterface::ORDER_COUNTRY_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_TRANSPORT => ConfigDefaultInterface::ORDER_TRANSPORT_MAP,
            ConfigDefaultInterface::FIELD_TYPE_DYNAMIC_SELECT => $this->getDynamicSelectOptionsByField($fieldId, $tableScope),
            ConfigDefaultInterface::FIELD_TYPE_INVOICE => ConfigDefaultInterface::AVAILABLE_INVOICE_STATUS_SELECT,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE => $this->getSelectWarehouseOptions(),
            default => [],
        };

        return $inputSelect;
    }

    public function getDynamicSelectOptionsByField(int $fieldId, string $tableScope): array
    {
        return match($tableScope) {
            TableConfig::MAIN_TABLE_NAME => OrderData::where('field_id', $fieldId)
                ->orderBy('value')
                ->distinct()
                ->pluck('value')
                ->all(),
            ItemsTableConfig::TABLE_NAME => OrderItemData::where('field_id', $fieldId)
                ->orderBy('value')
                ->distinct()
                ->pluck('value')
                ->all(),
        };
    }

    private function getOrderItems(Order $order): array
    {
        $data = [];

        foreach ($order->items as $index => $item) {
            $itemData = [];
            $itemNameFieldId = OrderItemService::getItemNameField()->id;
            $itemName = OrderItemData::where('field_id', $itemNameFieldId)->where('order_item_id', $item->id)->first()->value;
            $prefixedName = sprintf('%s. %s', $index + 1, $itemName);

            foreach (TableService::getItemsTable()->fields as $field) {
                $orderItemDataEntity = OrderItemData::where('order_item_id', $item->id)->where('field_id', $field->id)->first();

                if ($orderItemDataEntity) {
                    $itemData['details'][] = [
                        'value' => $orderItemDataEntity->value,
                        'field_id' => $field->id,
                        'field_name' => $field->name,
                        'field_type' => $field->type,
                        'field_order' => $field->order,
                        'updated_at' => $orderItemDataEntity->updated_at,
                        'updated_by' => $orderItemDataEntity->lastUpdatedBy?->name,
                        'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, ItemsTableConfig::TABLE_NAME),
                        'is_from_warehouse' => $item->is_taken_from_warehouse,
                    ];
                } else {
                    $itemData['details'][] = [
                        'order_data_id' => '',
                        'value' => '',
                        'field_id' => $field->id,
                        'field_name' => $field->name,
                        'field_type' => $field->type,
                        'field_order' => $field->order,
                        'updated_at' => null,
                        'updated_by' => null,
                        'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, ItemsTableConfig::TABLE_NAME),
                        'is_from_warehouse' => $item->is_taken_from_warehouse,
                    ];
                }
            }
            $itemData['settings']['collapse_id'] = preg_replace('/[\s.]+/', '', $prefixedName);
            $itemData['settings']['item_id'] = $item->id;
            $itemData['settings']['purchase_sum'] = $item->getPurchaseSum();
            $itemData['settings']['purchase_sum_field_name'] = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)->name;
            $itemData['settings']['sales_sum'] = $item->getSalesSum();
            $itemData['settings']['sales_sum_field_name'] = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SALES_SUM)->name;
            $itemData['settings']['is_locked'] = $item->is_locked;

            $itemData['buyers'] = $this->collectItemBuyers($item);

            $data[$prefixedName] = $itemData;
        }

        return $data;
    }

    protected function getFieldAdditionalData(TableField $field, int $orderId, ?OrderData $orderDataEntity = null): array
    {
        return match ($field->type) {
            ConfigDefaultInterface::FIELD_TYPE_INVOICE => $this->getInvoiceData($orderDataEntity?->value, $field, $orderId),
            ConfigDefaultInterface::FIELD_TYPE_DUTY_7, ConfigDefaultInterface::FIELD_TYPE_DUTY_15 => $this->getAdditionalDutyData($field, $orderDataEntity),
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_2 => $this->getAdditionalTransportPrice2Data($orderId),
            default => [],
        };
    }

    protected function getInvoiceData(?string $invoiceNumber, TableField $field, int $orderId): array
    {
        // Retrieve the invoice by invoice number or return null if not found.
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

        // Use ternary operator to assign data or default values.
        $data = $invoice ? $invoice->only(['id', 'invoice_number', 'issue_date', 'pay_until_date', 'status', 'sum']) : [
            'invoice_number' => null,
            'issue_date' => null,
            'pay_until_date' => null,
            'status' => null,
            'id' => null,
            'sum'=> null,
        ];

        $data['sum'] = number_format($data['sum'], 2, '.', '');
        $data['display_class'] = InvoiceService::getInvoiceDisplayColor($data['invoice_number']);
        $data['auto_calculated_sum'] = TableService::getSumRepresentationForInvoiceField($field, $orderId);

        return $data;
    }

    private function collectItemBuyers(OrderItem $item): array
    {
        $buyers = [];
        foreach ($item->buyers as $buyer) {
            $buyers[] = [
                'id' => $buyer->id,
                'name' => $buyer->name,
                'quantity' => $buyer->quantity,
                'address' => $buyer->address,
                'last_country' => $buyer->last_country,
                'dep_country' => $buyer->dep_country,
                'carrier' => $buyer->carrier,
                'trans_number' => $buyer->trans_number,
                'load_date' => $buyer->load_date,
                'delivery_date' => $buyer->delivery_date,
            ];
        }

        return $buyers;
    }

    private function getAdditionalDutyData(TableField $field, ?OrderData $orderDataEntity = null): array
    {
        if (!$orderDataEntity) {
            return [];
        }

        $orderId = $orderDataEntity->order->id;

        $settings = [];
        foreach ($field->settings()->where('order_id', $orderId)->get() as $setting) {
            $settings[$setting->setting] = $setting->value;
        }

        return [
            'settings' => $settings
        ];
    }

    private function collectBuyerInvoices(Order $order): array
    {
        $buyers = [];
        $itemSellPriceFieldId = TableField::where('type', ConfigDefaultInterface::FIELD_TYPE_SALES_NUMBER)->first()->id;

        foreach ($order->items as $item) {
            $itemSellPrice = OrderItemData::where('order_item_id', $item->id)->where('field_id', $itemSellPriceFieldId)->first()?->value;
            $itemSellPrice = (float) $itemSellPrice;

            foreach ($item->buyers as $buyer) {
                $buyers[$buyer->name]['price_for_items'][] = $itemSellPrice * $buyer->quantity;
                $buyers[$buyer->name]['buyer_id'] = $buyer->id;

            }
        }

        // Format results
        $results = [];
        foreach ($buyers as $buyer => $data) {
            $totalPrice = 0.0;
            $buyerId = $data['buyer_id'];

            foreach ($data['price_for_items'] as $price) {
                $totalPrice += $price;
            }

            $results[$buyer] = [
                'total_price' => number_format($totalPrice, 2, '.', ''),
                'buyer_id' => $buyerId,
                'order_id' => $order->id,
                'invoice' => $this->getInvoiceForBuyer($order->id, $buyer),
                'invoice_transport' => $this->getInvoiceForBuyerTransport($order->id, $buyer),
            ];
        }

        return $results;
    }

    private function getInvoiceForBuyer(int $orderId, string $buyer): array
    {
        $data = [
            'number' => null,
            'status' => null,
            'issue_date' => null,
            'pay_until_date' => null,
            'sum' => '0.00'
        ];

        $invoice = Invoice::where('order_id', $orderId)->where('customer', $buyer)->first();

        if ($invoice) {
            $data = [
                'number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'issue_date' => $invoice->issue_date,
                'pay_until_date' => $invoice->pay_until_date,
                'display_class' => InvoiceService::getInvoiceDisplayColor($invoice->invoice_number),
                'sum' => number_format($invoice->sum, 2, '.', ''),
            ];
        }

        return $data;
    }

    private function getInvoiceForBuyerTransport(int $orderId, string $buyer): array
    {
        $data = [
            'number' => null,
            'status' => null,
            'issue_date' => null,
            'pay_until_date' => null,
            'sum' => '0.00'
        ];

        $invoice = Invoice::where('order_id', $orderId)->where('customer', $buyer . ' Trans')->where('is_trans', true)->first();

        if ($invoice) {
            $data = [
                'number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'issue_date' => $invoice->issue_date,
                'pay_until_date' => $invoice->pay_until_date,
                'display_class' => InvoiceService::getInvoiceDisplayColor($invoice->invoice_number),
                'sum' => number_format($invoice->sum, 2, '.', ''),
            ];
        }

        return $data;
    }

    private function getSelectWarehouseOptions(): array
    {
        $data = [];
        foreach (Warehouse::orderBy('is_active','desc')->get() as $warehouse) {
            $data[$warehouse->id] = $warehouse->is_active ? $warehouse->name : sprintf('%s (Neveikiantis)', $warehouse->name);
        }

        return $data;
    }

    public function getRelatedOrderHierarchy(Order $order): array
    {
        $rootOrder = $this->findRootOrder($order);
        $hierarchy = [$this->getOrderHierarchy($rootOrder)];

        $formatted = [];
        $this->flattenData($hierarchy, null, $formatted);

        return $formatted;
    }

    private  function flattenData(array $items, $parentId = null, &$formatted)
    {
        foreach ($items as $item) {
            $formatted[] = [
                'id' => $item['order_id'],
                'parent' => $parentId,
                'name' => $item['order_key']
            ];
            if (!empty($item['childrenData'])) {
                $this->flattenData($item['childrenData'], $item['order_id'], $formatted);
            }
        }
    }


    private function findRootOrder(Order $order): Order
    {
        $rootOrder = $order;

        if ($order->parent_id !== null) {
            $rootOrder = $this->findRootOrder($order->parent);
        };

        return $rootOrder;
    }

    private function getOrderHierarchy(Order $rootOrder): array {
        // Starts the process with the root order
        $generationalHierarchy = $this->addOrderToHierarchy($rootOrder);

        return $generationalHierarchy;
    }

    private function addOrderToHierarchy(Order $order): array {
        $orderDetails = [
            'order_id' => $order->id,
            'order_key' => $order->getKeyField(), // Assuming getKeyField() is a method to get the key
            'childrenData' => []
        ];

        // Check if there are children and collect their details recursively
        foreach ($order->children as $childOrder) {
            $orderDetails['childrenData'][] = $this->addOrderToHierarchy($childOrder);
        }

        return $orderDetails;
    }

    private function getRelatedOrderParentLinks($order): array
    {
        $data = [];
        if ($order->parent_id !== null) {
            $this->getParentOrderLink($order->parent, $data);
        }

        $data[] = [
            'order_id' => $order->id,
            'order_key' => $order->getKeyField(),
        ];


        return $data;
    }

    private function getParentOrderLink(Order $order, array &$data): array
    {
        if ($order->parent_id !== null) {
            $this->getParentOrderLink($order->parent, $data);
        }

        $data[] = [
            'order_id' => $order->id,
            'order_key' => $order->getKeyField(),
        ];

        return $data;
    }

    private function getRelatedOrderChildrenLinks(Order $order): array
    {
        $childrenData = [];
        foreach ($order->children as $child) {
            $childrenData[] = [
                'order_id' => $child->id,
                'order_key' => $child->getKeyField(),
            ];
        }

        return [
            'order_id' => $order->id,
            'order_key' => $order->getKeyField(),
            'children' => $childrenData,
        ];
    }

    private function retrieveOrderComments(Order $order): array
    {
        $comments = [];
        foreach ($order->comments as $comment) {
            $comments[] = [
                'id' => $comment->id,
                'content' => $comment->content,
                'author' => $comment->user->name,
                'created_at' => $comment->created_at->format('Y-m-d H:i'),
            ];
        }

        return $comments;
    }

    private function getAdditionalTransportPrice2Data(int $orderId): array
    {
        $invoices = Invoice::where('order_id', $orderId)->where('is_trans', true)->get();
        $data = [];
        foreach ($invoices as $invoice) {
            $data[] = [
                'name' => $invoice->customer,
                'number' => $invoice->invoice_number,
                'sum' => $invoice->sum,
            ];
        }

        return $data;
    }
}
