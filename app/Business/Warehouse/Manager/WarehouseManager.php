<?php

namespace App\Business\Warehouse\Manager;

use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\TableField;
use App\Models\Warehouse\Warehouse;
use App\Service\TableService;
use Illuminate\Database\Eloquent\Collection;
use shared\ConfigDefaultInterface;

class WarehouseManager
{
    public function collectWarehouseItems(Warehouse $warehouse): array
    {
        $warehouseDataFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE)->id;

        $itemIds = OrderItemData::where('field_id', $warehouseDataFieldId)
            ->where('value', $warehouse->id)
            ->pluck('order_item_id')
            ->toArray();

        $totalItemQuantity = 0;
        $totalWorth = 0.00;
        $items = [];
        foreach ($itemIds as $itemId) {
            $itemAmount = (int) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE)?->value;
            if (!$itemAmount) {
                continue;
            }

            $itemPrice = (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_NUMBER)?->value;
            $itemWorth = $itemPrice * $itemAmount;

            $totalWorth += $itemWorth;
            $totalItemQuantity += $itemAmount;

            $items[] = [
                'name' => $this->getItemFieldDataByIdentifier($itemId, ConfigDefaultInterface::FIELD_IDENTIFIER_ITEM_NAME)?->value,
                'amount' => $itemAmount,
                'price' => number_format($itemPrice, 2, '.', ''),
                'total_price' => number_format($itemWorth, 2, '.', ''),
                'measurement_unit' => $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_SELECT_MEASUREMENT)?->value,
                'glue' => $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_SELECT_GLUE)?->value,
                'measurement' => $this->getItemFieldDataByIdentifier($itemId, ConfigDefaultInterface::FIELD_IDENTIFIER_ITEM_MEASUREMENTS)?->value,
                'seller' => $this->getItemFieldDataByIdentifier($itemId, ConfigDefaultInterface::FIELD_IDENTIFIER_ITEM_SELLER)?->value,
                'order' => $this->getOrderDataByItemId($itemId),
                'item_id' => $itemId,
            ];
        }

        return [
            'items' => $items,
            'total_worth' => number_format($totalWorth, 2, '.', ''),
            'total_quantity' => $totalItemQuantity,
        ];
    }

    public function collectWarehouseInStockItemCount(): array
    {
        $warehouseDataFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE)->id;
        $warehouseIds = Warehouse::all()->pluck('id')->toArray();

        $data = [];
        foreach ($warehouseIds as $warehouseId) {
            $totalItemQuantity = 0;
            $itemIds = OrderItemData::where('field_id', $warehouseDataFieldId)
                ->where('value', $warehouseId)
                ->pluck('order_item_id')
                ->toArray();
            foreach ($itemIds as $itemId) {
                $itemAmount = (int)$this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE)?->value;
                $totalItemQuantity += $itemAmount;
            }

            $data[$warehouseId] = $totalItemQuantity;
        }

        return $data;
    }

    protected function getItemFieldDataByType(int $orderItemId, string $targetField): ?OrderItemData
    {
        $targetFieldId = TableField::where('type', $targetField)->first()?->id;

        return OrderItemData::where('order_item_id', $orderItemId)->where('field_id', $targetFieldId)->first();
    }

    protected function getItemFieldDataByIdentifier(int $orderItemId, string $targetField): ?OrderItemData
    {
        $targetFieldId = TableField::where('identifier', $targetField)->first()?->id;

        return OrderItemData::where('order_item_id', $orderItemId)->where('field_id', $targetFieldId)->first();
    }

    protected function getOrderDataByItemId(int $itemId): array
    {
        $order = OrderItem::find($itemId)->order;

        return [
            'id' => $order->id,
            'key' => $order->getKeyField(),
        ];
    }
}
