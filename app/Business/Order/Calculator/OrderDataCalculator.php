<?php

namespace App\Business\Order\Calculator;

use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\FieldSettings;
use App\Models\Table\TableField;
use App\Service\TableService;
use shared\ConfigDefaultInterface;

class OrderDataCalculator
{
    public function calculateTotalPurchaseSum(Order $order): void
    {
        $sum = 0.0;
        foreach ($order->items as $item) {
            $sum += (float) $item->getPurchaseSum();
        }

        $formattedResult = number_format($sum, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TOTAL_PURCHASE_SUM);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_TOTAL_PURCHASE_SUM)->id,
            ];

            $order->data()->create($data);
        }
    }

    public function calculateDuty7(Order $order): void
    {
        // 1 collect needed fields purchase number and amount
        $purchaseSum = (float) $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TOTAL_PURCHASE_SUM)?->value;
        if (!$purchaseSum) {
            $purchaseSum = 0.0;
        }

        $transportPrice1BeforeEs = (float) $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1_BEFORE_ES)?->value;
        if (!$transportPrice1BeforeEs) {
            $transportPrice1BeforeEs = 0.0;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseSum;
        $secondNumber = (float) $transportPrice1BeforeEs;

        $result = ($firstNumber + $secondNumber) * 0.07;
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_DUTY_7);

        // Check if calculation is disabled
        $calculationIsDisabled = FieldSettings::where('order_id', $order->id)
            ->where('field_id', $orderFieldData?->field_id)
            ->where('setting', ConfigDefaultInterface::AUTO_CALCULATION_SETTING)
            ->first()?->value;

        $calculationIsDisabled = (bool) $calculationIsDisabled;
        if ($calculationIsDisabled) {
            $formattedResult = '0.00';
        }

        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_DUTY_7)->id,
            ];

            $order->data()->create($data);
        }
    }

    public function calculateDuty15(Order $order): void
    {
        // 1 collect needed fields purchase number and amount
        $purchaseSum = (float) $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TOTAL_PURCHASE_SUM)?->value;
        if (!$purchaseSum) {
            $purchaseSum = 0.0;
        }

        $transportPrice1BeforeEs = (float) $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1_BEFORE_ES)?->value;
        if (!$transportPrice1BeforeEs) {
            $transportPrice1BeforeEs = 0.0;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseSum;
        $secondNumber = (float) $transportPrice1BeforeEs;

        $result = ($firstNumber + $secondNumber) * 0.158;
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_DUTY_15);

        // Check if calculation is disabled
        $calculationIsDisabled = FieldSettings::where('order_id', $order->id)
            ->where('field_id', $orderFieldData?->field_id)
            ->where('setting', ConfigDefaultInterface::AUTO_CALCULATION_SETTING)
            ->first()?->value;

        $calculationIsDisabled = (bool) $calculationIsDisabled;
        if ($calculationIsDisabled) {
            $formattedResult = '0.00';
        }

        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_DUTY_15)->id,
            ];

            $order->data()->create($data);
        }
    }

    public function calculatePrimeCost(Order $order): void
    {
        $primeCostComponents = [
            ConfigDefaultInterface::FIELD_TYPE_TOTAL_PURCHASE_SUM,
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1_BEFORE_ES,
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1,
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_2,
            ConfigDefaultInterface::FIELD_TYPE_DUTY_7,
            ConfigDefaultInterface::FIELD_TYPE_DUTY_15,
            ConfigDefaultInterface::FIELD_TYPE_TAX_DIFF,
            ConfigDefaultInterface::FIELD_TYPE_BROKER,
            ConfigDefaultInterface::FIELD_TYPE_WAREHOUSES,
            ConfigDefaultInterface::FIELD_TYPE_BANK,
            ConfigDefaultInterface::FIELD_TYPE_OTHER_COSTS,
            ConfigDefaultInterface::FIELD_TYPE_FLAW,
            ConfigDefaultInterface::FIELD_TYPE_AGENT,
            ConfigDefaultInterface::FIELD_TYPE_FACTORING,
        ];
        $primeCost = 0.00;

        foreach ($primeCostComponents as $costField) {
            $value = $this->getOrderFieldData($order, $costField)?->value;
            if (!$value) {
                continue;
            }

            $primeCost += (float) $value;
        }

        $primeCostFormatted = number_format($primeCost, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PRIME_COST);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $primeCostFormatted,
            ]);
        } else {
            $data = [
                'value' => $primeCostFormatted,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PRIME_COST)->id,
            ];

            $order->data()->create($data);
        }
    }

    public function calculateTotalSalesSum(Order $order): void {
        $sum = 0.0;
        foreach ($order->items as $item) {
            $sum += (float) $item->getSalesSum();
        }

        $formattedResult = number_format($sum, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_SUM);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_SUM)->id,
            ];

            $order->data()->create($data);
        }
    }

    public function calculateTotalProfit(Order $order): void
    {
        // 1 collect needed fields purchase number and amount
        $totalSalesSum = (float) $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_SUM)?->value;
        if (!$totalSalesSum) {
            $totalSalesSum = 0.0;
        }

        $primeCost = (float) $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PRIME_COST)?->value;
        if (!$primeCost) {
            $primeCost = 0.0;
        }

        $profit = $totalSalesSum - $primeCost;
        $formattedResult = number_format($profit, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PROFIT);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PROFIT)->id,
            ];

            $order->data()->create($data);
        }
    }

    public function calculateOtherCosts(Order $order): void
    {
        $otherCosts = 0;
        foreach ($order->items as $item) {
            $otherCosts += $item->getItemOtherCosts();
        }

        $formattedResult = number_format($otherCosts, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_OTHER_COSTS);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_OTHER_COSTS)->id,
            ];

            $order->data()->create($data);
        }
    }

    public function calculatePurchaseSumForItem(OrderItem $orderItem): void
    {
        $purchaseNumber = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_NUMBER)?->value;
        if (!$purchaseNumber) {
            return;
        }

        $amount = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_AMOUNT)?->value;
        if (!$amount) {
            return;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseNumber;
        $secondNumber = (float) $amount;

        $result = ($firstNumber * $secondNumber);
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)->id,
            ];

            $orderItem->data()->create($data);
        }
    }

    public function calculateSalesSumForItem(OrderItem $orderItem): void
    {
        $salesNumber = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_SALES_NUMBER)?->value;
        if (!$salesNumber) {
            return;
        }

        $amount = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_AMOUNT)?->value;
        if ($amount === null) {
            return;
        }

        // Convert strings to floats
        $firstNumber = (float) $salesNumber;
        $secondNumber = (float) $amount;

        $result = ($firstNumber * $secondNumber);
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_SALES_SUM);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SALES_SUM)->id,
            ];

            $orderItem->data()->create($data);
        }
    }

    public function calculateTotalSalesAmount(OrderItem $orderItem): void
    {
        if (!$orderItem->exists) {
            return;
        }

        $totalAmount = $orderItem->buyers->sum('quantity');
        $totalItemSalesAmount = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_AMOUNT);
        if ($totalItemSalesAmount !== null) {
            $totalItemSalesAmount->update([
                'value' => (float) $totalAmount,
            ]);
        } else {
            $data = [
                'value' => (float) $totalAmount,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_AMOUNT)->id,
            ];

            $orderItem->data()->create($data);
        }
    }

    public function calculateQuantityGoingToWarehouse(OrderItem $orderItem): void
    {
        if (!$orderItem->exists) {
            return;
        }

        if ($orderItem->is_taken_from_warehouse) {
            return;
        }

        // Check if warehouse is assigned
        $warehouseIsAssigned = (bool) $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_SELECT_WAREHOUSE)?->value;

        $availableQuantity = (float) $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_AMOUNT)?->value;
        $totalSalesQuantity = (float) $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_AMOUNT)?->value;
        $value = $availableQuantity - $totalSalesQuantity;

        if ($value < 0) {
            $value = sprintf('%s (Nepakankamas pirkimo kiekis)', $value);
        }

        if (!$warehouseIsAssigned) {
            $value = sprintf('%s (Sandėlis nepriskirtas)', $value);
        }

        $quantityGoingToWarehouse = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE);
        if ($quantityGoingToWarehouse) {
            $quantityGoingToWarehouse->update([
                'value' => $value,
            ]);
        } else {
            $data = [
                'value' => $value,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_AMOUNT_TO_WAREHOUSE)->id,
            ];

            $orderItem->data()->create($data);
        }
    }

    public function calculateAvailableQuantityFromWarehouse(OrderItem $orderItem): void
    {
        if (!$orderItem->exists) {
            return;
        }

        if (!$orderItem->is_taken_from_warehouse) {
            return;
        }

        $quantityTakenFromWarehouse = (float) $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_AMOUNT_FROM_WAREHOUSE)?->value;
        $totalSalesQuantity = (float) $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_AMOUNT)?->value;
        $value = $quantityTakenFromWarehouse - $totalSalesQuantity;

        $availableQuantityDataEntity = $this->getItemFieldData($orderItem, ConfigDefaultInterface::FIELD_TYPE_AVAILABLE_AMOUNT_FROM_WAREHOUSE);

        $availableQuantityDataEntity->update([
            'value' => $value,
        ]);
    }

    protected function getOrderFieldData(Order $order, string $targetField): ?OrderData
    {
        $targetFieldId = TableField::where('type', $targetField)->first()?->id;

        return OrderData::where('order_id', $order->id)->where('field_id', $targetFieldId)->first();
    }

    protected function getItemFieldData(OrderItem $orderItem, string $targetField): ?OrderItemData
    {
        $targetFieldId = TableField::where('type', $targetField)->first()?->id;

        return OrderItemData::where('order_item_id', $orderItem->id)->where('field_id', $targetFieldId)->first();
    }
}
