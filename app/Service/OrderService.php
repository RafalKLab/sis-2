<?php

namespace App\Service;

use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Table\TableField;
use shared\ConfigDefaultInterface;

class OrderService
{
    public static function getKeyField(): ?TableField
    {
        return TableField::where('type', 'id')->first();
    }

    public static function getKeyFieldFrom(int $orderId): OrderData
    {
        $keyField = OrderService::getKeyField();
        if (!$keyField) {
            throw new \Exception('Key order field not exists');
        }
        $keyId = $keyField->id;

        $targetOrderKeyField = OrderData::where('order_id', $orderId)->where('field_id', $keyId)->first();
        if (!$targetOrderKeyField) {
            throw new \Exception('Key order field not exists');
        }

        return $targetOrderKeyField;
    }

    public static function getOrderStatuses(array $orderIds): array
    {
        $orderStatusFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SELECT_STATUS)->id;

        return OrderData::where('field_id', $orderStatusFieldId)
            ->whereIn('order_id', $orderIds)
            ->where('value', ConfigDefaultInterface::ORDER_STATUS_PAID)
            ->pluck('value', 'order_id')
            ->toArray();
    }

    public static function getOrderPrimeCost(int $orderId): float
    {
        $primeCostFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PRIME_COST)->id;

        return OrderData::where('order_id', $orderId)->where('field_id', $primeCostFieldId)->first()?->value;
    }

    public static function generateKeyField(int $orderId): string
    {
        $orderId = (string) $orderId;

        return 'KP-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
    }

    public static function generateKeyFieldForChild(int $orderId): string
    {
        $currentOrder = Order::find($orderId);
        $parentOrder =  $currentOrder->parent;
        $parentKeyFieldValue = OrderService::getKeyFieldFrom($parentOrder->id)->value;
        $childrenCount = $parentOrder->children()->count();

        return sprintf('%s-%s', $parentKeyFieldValue, $childrenCount);
    }
}
