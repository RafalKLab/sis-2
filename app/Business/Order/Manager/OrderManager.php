<?php

namespace App\Business\Order\Manager;

use App\Business\Table\Config\ItemsTableConfig;
use App\Business\Table\Config\TableConfig;
use App\Models\Order\Invoice;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\TableField;
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
                    'additional_data' => $this->getFieldAdditionalData($field, $orderDataEntity),
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
                    'additional_data' => $this->getFieldAdditionalData($field),
                ];
            }
        }

        return $orderData;
    }

    public function getOrderDetailsWithGroups($order): array
    {
        $orderData = [
            'id' => $order->id,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'uploaded_files' => $order->files()->count(),
            'user' => $order->user->name,
            'items' => $this->getOrderItems($order),
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
                    'additional_data' => $this->getFieldAdditionalData($field, $orderDataEntity),
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
                    'additional_data' => $this->getFieldAdditionalData($field),
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
            default => [],
        };

        return $inputSelect;
    }

    protected function getDynamicSelectOptionsByField(int $fieldId, string $tableScope): array
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
                    ];
                }
            }
            $itemData['settings']['collapse_id'] = preg_replace('/[\s.]+/', '', $prefixedName);
            $itemData['settings']['item_id'] = $item->id;
            $itemData['settings']['purchase_sum'] = $item->getPurchaseSum();
            $itemData['settings']['purchase_sum_field_name'] = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)->name;
            $itemData['settings']['sales_sum'] = $item->getSalesSum();
            $itemData['settings']['sales_sum_field_name'] = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SALES_SUM)->name;

            $itemData['buyers'] = $this->collectItemBuyers($item);

            $data[$prefixedName] = $itemData;
        }

        return $data;
    }

    protected function getFieldAdditionalData(TableField $field, ?OrderData $orderDataEntity = null): array
    {
        return match ($field->type) {
            ConfigDefaultInterface::FIELD_TYPE_INVOICE => $this->getInvoiceData($orderDataEntity?->value),
            ConfigDefaultInterface::FIELD_TYPE_DUTY_7, ConfigDefaultInterface::FIELD_TYPE_DUTY_15 => $this->getAdditionalDutyData($field, $orderDataEntity),
            default => [],
        };
    }

    protected function getInvoiceData(?string $invoiceNumber): array
    {
        // Retrieve the invoice by invoice number or return null if not found.
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

        // Use ternary operator to assign data or default values.
        $data = $invoice ? $invoice->only(['id', 'invoice_number', 'issue_date', 'pay_until_date', 'status']) : [
            'invoice_number' => null,
            'issue_date' => null,
            'pay_until_date' => null,
            'status' => null,
            'id' => null,
        ];

        $data['display_class'] = InvoiceService::getInvoiceDisplayColor($data['invoice_number']);

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
}
