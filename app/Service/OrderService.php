<?php

namespace App\Service;

use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Table\TableField;

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
