<?php

namespace App\Business\Warehouse\Manager;

use App\Business\Note\Manager\NoteManager;
use App\Models\Note\Note;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\FieldSettings;
use App\Models\Table\TableField;
use App\Models\User;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseItem;
use App\Models\Warehouse\WarehouseStock;
use App\Service\OrderService;
use App\Service\TableService;

use App\Service\WarehouseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use shared\ConfigDefaultInterface;

class WarehouseManager
{
    protected int $itemsExceedingDeadline = 0;

    protected NoteManager $noteManager;

    /**
     * @param NoteManager $noteManager
     */
    public function __construct(NoteManager $noteManager)
    {
        $this->noteManager = $noteManager;
    }

    public function collectAllWarehouseItems(): array
    {
        $warehouses = Warehouse::where('is_active', 1)->get();
        $data = [];
        foreach ($warehouses as $warehouse) {
            $data[$warehouse->name] = $this->collectWarehouseItems($warehouse);
        }

        return $data;
    }

    public function collectWarehouseItems(Warehouse $warehouse, ?array $itemIds = null): array
    {
        $warehouseDataFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE)->id;

        if ($itemIds === null) {
            $itemIds = OrderItemData::where('field_id', $warehouseDataFieldId)
                ->where('value', $warehouse->id)
                ->pluck('order_item_id')
                ->toArray();
        }

        $totalItemQuantity = 0.0;
        $totalWorth = 0.00;
        $items = [];
        $availableItemIds = [];
        foreach ($itemIds as $itemId) {

            $item = OrderItem::find($itemId);
            if ($item->is_taken_from_warehouse) {
                continue;
            }

            $itemAmount = WarehouseService::getItemStock($itemId, $warehouse->id);

            if (!$itemAmount) {
                continue;
            }

            $itemPrice = (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_NUMBER)?->value;
            $itemPrimeCost = $this->calculateItemPrimeCost($itemPrice, $itemId);

            $itemWorth = $itemPrimeCost * $itemAmount;

            $totalWorth += $itemWorth;
            $totalItemQuantity += $itemAmount;

            $tentativeDate = $this->getItemTentativeDate($itemId, $warehouse);
            $exceededDeadline = $this->checkDeadline($tentativeDate);
            if ($exceededDeadline) {
                $this->itemsExceedingDeadline ++;
            }

            $availableItemIds[] = $itemId;

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
                'quality' => $this->getItemFieldDataByIdentifier($itemId, ConfigDefaultInterface::FIELD_IDENTIFIER_ITEM_QUALITY)?->value,
                'prime_cost' => $itemPrimeCost,
                'tentative_date' => $tentativeDate,
                'exceeded_deadline' => $exceededDeadline,
                'comment' => $this->getItemLastComment($itemId),
            ];
        }

        $this->cleanPurchasedItemsTentativeDates($availableItemIds, $warehouse);

        return [
            'items' => $items,
            'total_worth' => number_format($totalWorth, 2, '.', ''),
            'total_quantity' => $totalItemQuantity,
            'items_exceeding_deadline' => $this->itemsExceedingDeadline,
        ];
    }

    public function collectWarehouseInStockItemCount(): array
    {
        $warehouseDataFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE)->id;
        $warehouseIds = Warehouse::all()->pluck('id')->toArray();

        $data = [];
        foreach ($warehouseIds as $warehouseId) {
            $totalItemQuantity = 0.0;
            $itemIds = OrderItemData::where('field_id', $warehouseDataFieldId)
                ->where('value', $warehouseId)
                ->pluck('order_item_id')
                ->toArray();
            foreach ($itemIds as $itemId) {
                $item = OrderItem::find($itemId);
                if ($item->is_taken_from_warehouse) {
                    continue;
                }

                $itemAmount = WarehouseService::getItemStock($itemId, $warehouseId);
                $totalItemQuantity += $itemAmount;
            }

            $data[$warehouseId] = $totalItemQuantity;
        }

        return $data;
    }

    public function calculateItemPrimeCost(float $itemPrice, int $itemId): string
    {
        $itemInitialStock = WarehouseService::getItemInitialStock($itemId);

        $orderId = $this->getOrderDataByItemId($itemId)['id'];
        $sum = 0.0;

        // Calculate duty 7
        $field = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_DUTY_7);
        $calculationIsDisabled = FieldSettings::where('field_id', $field->id)
            ->where('order_id', $orderId)
            ->where('setting', ConfigDefaultInterface::AUTO_CALCULATION_SETTING)
            ->first()?->value;
        if (!$calculationIsDisabled) {
            $sum += $itemPrice / 100 * 7;
        }

        // Calculate duty 15
        $field = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_DUTY_15);
        $calculationIsDisabled = FieldSettings::where('field_id', $field->id)
            ->where('order_id', $orderId)
            ->where('setting', ConfigDefaultInterface::AUTO_CALCULATION_SETTING)
            ->first()?->value;
        if (!$calculationIsDisabled) {
            $sum += $itemPrice / 100 * 15.8;
        }

        // Apply item other costs
        $sum += (float) $this->getItemFieldDataByType($itemId, ConfigDefaultInterface::FIELD_TYPE_ITEM_OTHER_COSTS)?->value;

        $costsSum = 0.0;
        // Apply additional costs like transportation and divide by amount
        $costs = [
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1,
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1_BEFORE_ES,
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_2,
            ConfigDefaultInterface::FIELD_TYPE_BROKER,
            ConfigDefaultInterface::FIELD_TYPE_WAREHOUSES,
            ConfigDefaultInterface::FIELD_TYPE_BANK,
            ConfigDefaultInterface::FIELD_TYPE_FLAW,
            ConfigDefaultInterface::FIELD_TYPE_AGENT,
            ConfigDefaultInterface::FIELD_TYPE_FACTORING,
        ];

        foreach ($costs as $cost) {
            $costsSum += (float) $this->getOrderFieldDataByType($orderId, $cost)?->value;
        }

        $primeCost = $itemPrice + $sum + ($costsSum / $itemInitialStock);

        return number_format($primeCost, 2, '.', '');
    }

    protected function getOrderFieldDataByType(int $orderId, string $targetField): ?OrderData
    {
        $targetFieldId = TableField::where('type', $targetField)->first()?->id;

        return OrderData::where('order_id', $orderId)->where('field_id', $targetFieldId)->first();
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

    public function collectWarehouseStockOverview(int $warehouseId): array
    {
        $warehouseStockEntities = WarehouseStock::where('warehouse_id', $warehouseId)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->toArray();

        // Populate with additional data
        foreach ($warehouseStockEntities as &$stockEntity) {
            $stockEntity['order_key'] = OrderService::getKeyFieldFrom($stockEntity['order_id'])->value;
            $stockEntity['item_name'] = OrderItem::find($stockEntity['warehouse_item_id'])?->getNameField();
        }
        unset($stockEntity);

        return $warehouseStockEntities;
    }

    protected function getItemTentativeDate(int $itemId, Warehouse $warehouse): ?string
    {
        return optional(WarehouseItem::where('order_item_id', $itemId)->first())->tentative_date;
    }

    protected function checkDeadline(?string $tentativeDate): bool
    {
        if ($tentativeDate === null) {
            return false;
        }

        $tentativeDateCarbon = Carbon::parse($tentativeDate)->startOfDay();
        $now = Carbon::now()->startOfDay();

        if ($now->gt($tentativeDateCarbon)) {
            return true;
        } else {
            return false;
        }

    }

    private function cleanPurchasedItemsTentativeDates(array $availableItemIds, Warehouse $warehouse): void
    {
        DB::transaction(function () use ($warehouse, $availableItemIds) {
            WarehouseItem::where('warehouse_name', $warehouse->name)
                ->whereNotIn('order_item_id', $availableItemIds)
                ->delete();
        });
    }

    private function getItemLastComment(int $itemId): ?Note
    {
        $notes = $this->noteManager->getNotesByIdentifierAndTarget(ConfigDefaultInterface::WAREHOUSE_ITEM_IDENTIFIER, $itemId);
        if (count($notes)) {
            $note = $notes[0];
            $note['author_email'] = User::find($note->author)->email;

            return $note;
        }

        return null;
    }
}
