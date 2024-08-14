<?php

namespace App\Business\Order\Calculator;

use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\TableField;
use App\Service\TableService;
use shared\ConfigDefaultInterface;

class OrderDataCalculator
{
    public function calculateTotalPurchaseSum(Order $order): void
    {
        $sum = 0;
        foreach ($order->items as $item) {
            $sum += $item->getPurchaseSum();
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
        $purchaseSum = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TOTAL_PURCHASE_SUM)?->value;
        if (!$purchaseSum) {
            $purchaseSum = 0.0;
        }

        $transportPrice1 = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1)?->value;
        if (!$transportPrice1) {
            $transportPrice1 = 0.0;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseSum;
        $secondNumber = (float) $transportPrice1;

        $result = ($firstNumber + $secondNumber) * 0.07;
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_DUTY_7);
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
        $purchaseSum = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TOTAL_PURCHASE_SUM)?->value;
        if (!$purchaseSum) {
            $purchaseSum = 0.0;
        }

        $transportPrice1 = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1)?->value;
        if (!$transportPrice1) {
            $transportPrice1 = 0.0;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseSum;
        $secondNumber = (float) $transportPrice1;

        $result = ($firstNumber + $secondNumber) * 0.158;
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_DUTY_15);
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
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1,
            ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_2,
            ConfigDefaultInterface::FIELD_TYPE_DUTY_7,
            ConfigDefaultInterface::FIELD_TYPE_DUTY_15,
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
        $sum = 0;
        foreach ($order->items as $item) {
            $sum += $item->getSalesSum();
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
        $totalSalesSum = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_SUM)?->value;
        if (!$totalSalesSum) {
            $totalSalesSum = 0.0;
        }

        $primeCost = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PRIME_COST)?->value;
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
        if (!$amount) {
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
        if ($totalItemSalesAmount) {
            $totalItemSalesAmount->update([
                'value' => $totalAmount,
            ]);
        } else {
            $data = [
                'value' => $totalAmount,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_TOTAL_SALES_AMOUNT)->id,
            ];

            $orderItem->data()->create($data);
        }
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
