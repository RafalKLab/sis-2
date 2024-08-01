<?php

namespace App\Business\Order\Manager;

use App\Business\Table\Config\ItemsTableConfig;
use App\Business\Table\Config\TableConfig;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItemData;
use App\Service\OrderItemService;
use App\Service\TableService;
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
                    'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, TableConfig::MAIN_TABLE_NAME)
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
                    'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, TableConfig::MAIN_TABLE_NAME)
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
                ];
            }
        }

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
                        'input_select' => $this->getInputSelectByFieldType($field->type, $field->id, ItemsTableConfig::TABLE_NAME)
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

            $data[$prefixedName] = $itemData;
        }

        return $data;
    }
}
