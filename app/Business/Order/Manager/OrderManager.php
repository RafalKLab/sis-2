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
                    'input_select' => $this->getInputSelectByFieldType($field->type)
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
                    'input_select' => $this->getInputSelectByFieldType($field->type),
                ];
            }
        }

        return $orderData;
    }

    protected function getInputSelectByFieldType(string $type): array
    {
        $inputSelect = match($type) {
            ConfigDefaultInterface::FIELD_TYPE_SELECT_STATUS => ConfigDefaultInterface::ORDER_STATUS_MAP,
            ConfigDefaultInterface::FIELD_TYPE_SELECT_GLUE => ConfigDefaultInterface::ORDER_GLUE_MAP,
            default => [],
        };

        return $inputSelect;
    }
}
