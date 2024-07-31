<?php

namespace App\Business\Order\Manager;

use App\Models\Order\Order;
use App\Models\Order\OrderData;
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
                    'input_select' => $this->getInputSelectByFieldType($field->type, $field->id)
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
                    'input_select' => $this->getInputSelectByFieldType($field->type, $field->id),
                ];
            }
        }

        return $orderData;
    }

    protected function getInputSelectByFieldType(string $type, int $fieldId): array
    {
        $inputSelect = match($type) {
            ConfigDefaultInterface::FIELD_TYPE_SELECT_STATUS => ConfigDefaultInterface::ORDER_STATUS_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_GLUE => ConfigDefaultInterface::ORDER_GLUE_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_MEASUREMENT => ConfigDefaultInterface::ORDER_MEASUREMENT_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_CERTIFICATION => ConfigDefaultInterface::ORDER_CERTIFICATION_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_COUNTRY => ConfigDefaultInterface::ORDER_COUNTRY_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_TRANSPORT => ConfigDefaultInterface::ORDER_TRANSPORT_MAP,
            ConfigDefaultInterface::FIELD_TYPE_DYNAMIC_SELECT => $this->getDynamicSelectOptionsByField($fieldId),
            default => [],
        };

        return $inputSelect;
    }

    protected function getDynamicSelectOptionsByField(int $fieldId): array
    {
        return OrderData::where('field_id', $fieldId)
            ->orderBy('value')
            ->distinct()
            ->pluck('value')
            ->all();
    }
}
