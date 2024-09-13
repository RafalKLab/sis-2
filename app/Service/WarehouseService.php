<?php

namespace App\Service;

use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Warehouse\WarehouseStock;
use shared\ConfigDefaultInterface;
use shared\ConfigWarehouseInterface;

class WarehouseService
{
    public static function updateItemWarehouseStock(OrderItem $item): void
    {
        $item->refresh();

        if ($item->is_taken_from_warehouse) {
            // Perform when taking from warehouse
            $warehouseItem = OrderItem::find($item->parent_item);

            $stockType = ConfigWarehouseInterface::ITEM_STOCK_TYPE_OUTGOING;
            $quantityFieldType = ConfigDefaultInterface::FIELD_TYPE_AMOUNT_FROM_WAREHOUSE;

            $warehouseStockItemEntity = new WarehouseStock();

            // Get order date
            $orderDateFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_ORDER_DATE)->id;
            $orderDate = OrderService::getOrderDataFor($orderDateFieldId, $item->order_id)?->value;
            if (!$orderDate) {
                $orderDate = date('Y-m-d');
            }

            // Get item warehouse
            $warehouseFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE)->id;
            $warehouseId = OrderItemData::where('field_id', $warehouseFieldId)->where('order_item_id', $item->id)->first()?->value;
            if (!$warehouseId) {
                if ($warehouseStockItemEntity->exists) {
                    $warehouseStockItemEntity->delete();
                }

                return;
            }

            // Get quantity
            $quantityFieldId = TableService::getFieldByType($quantityFieldType)->id;
            $quantity = OrderItemData::where('field_id', $quantityFieldId)->where('order_item_id', $item->id)->first()->value;

            $warehouseStockItemEntity->warehouse_id = $warehouseId;
            $warehouseStockItemEntity->item_id = $item->id;
            $warehouseStockItemEntity->warehouse_item_id = $warehouseItem->id;
            $warehouseStockItemEntity->order_id = $item->order_id;
            $warehouseStockItemEntity->type = $stockType;
            $warehouseStockItemEntity->quantity = $quantity;
            $warehouseStockItemEntity->date = $orderDate;
            $warehouseStockItemEntity->save();

        } else {
            // Perform when item is not from warehouse
            $stockType = ConfigWarehouseInterface::ITEM_STOCK_TYPE_INCOMING;
            $quantityFieldType = ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE;

            $warehouseStockItemEntity = WarehouseStock::where('item_id', $item->id)
                    ->where('type', $stockType)
                    ->first() ?? new WarehouseStock();


            // Get order date
            $orderDateFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_ORDER_DATE)->id;
            $orderDate = OrderService::getOrderDataFor($orderDateFieldId, $item->order_id)?->value;
            if (!$orderDate) {
                $orderDate = date('Y-m-d');
            }

            // Get item warehouse
            $warehouseFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE)->id;
            $warehouseId = OrderItemData::where('field_id', $warehouseFieldId)->where('order_item_id', $item->id)->first()?->value;
            if (!$warehouseId) {
                if ($warehouseStockItemEntity->exists) {
                    $warehouseStockItemEntity->delete();
                }

                return;
            }

            // Get quantity
            $quantityFieldId = TableService::getFieldByType($quantityFieldType)->id;
            $quantity = OrderItemData::where('field_id', $quantityFieldId)->where('order_item_id', $item->id)->first()->value;

            $warehouseStockItemEntity->warehouse_id = $warehouseId;
            $warehouseStockItemEntity->item_id = $item->id;
            $warehouseStockItemEntity->warehouse_item_id = $item->id;
            $warehouseStockItemEntity->order_id = $item->order_id;
            $warehouseStockItemEntity->type = $stockType;
            $warehouseStockItemEntity->quantity = $quantity;
            $warehouseStockItemEntity->date = $orderDate;
            $warehouseStockItemEntity->save();
        }
    }

    public static function getItemStock(int $itemId, int $warehouseId): int
    {
        $netQuantity = WarehouseStock::where('warehouse_item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->selectRaw('
                (SUM(CASE WHEN type = "incoming" THEN quantity ELSE 0 END) -
                SUM(CASE WHEN type = "outgoing" THEN quantity ELSE 0 END)) AS net_quantity
            ')
            ->value('net_quantity');

        return (int) $netQuantity;
    }
}
