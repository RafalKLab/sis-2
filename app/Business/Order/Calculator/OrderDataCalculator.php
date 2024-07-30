<?php

namespace App\Business\Order\Calculator;

use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Table\TableField;
use App\Service\TableService;
use shared\ConfigDefaultInterface;

class OrderDataCalculator
{
    public function calculatePurchaseSum(Order $order): void
    {
        $purchaseNumber = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_NUMBER)?->value;
        if (!$purchaseNumber) {
            return;
        }

        $amount = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_AMOUNT)?->value;
        if (!$amount) {
            return;
        }

        // Convert strings to floats
        $firstNumber = (float) $purchaseNumber;
        $secondNumber = (float) $amount;

        $result = ($firstNumber * $secondNumber);
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)->id,
            ];

            $order->data()->create($data);
        }
    }

    public function calculateDuty7(Order $order): void
    {
        // 1 collect needed fields purchase number and amount
        $purchaseSum = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)?->value;
        if (!$purchaseSum) {
            return;
        }

        $transportPrice1 = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1)?->value;
        if (!$transportPrice1) {
            return;
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
        $purchaseSum = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)?->value;
        if (!$purchaseSum) {
            return;
        }

        $transportPrice1 = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_TRANSPORT_PRICE_1)?->value;
        if (!$transportPrice1) {
            return;
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
            ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM,
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

    public function calculateSalesSum(Order $order): void {
        $salesNumber = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_SALES_NUMBER)?->value;
        if (!$salesNumber) {
            return;
        }

        $amount = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_AMOUNT)?->value;
        if (!$amount) {
            return;
        }

        // Convert strings to floats
        $firstNumber = (float) $salesNumber;
        $secondNumber = (float) $amount;

        $result = ($firstNumber * $secondNumber);
        $formattedResult = number_format($result, 2, '.', '');

        $orderFieldData = $this->getOrderFieldData($order, ConfigDefaultInterface::FIELD_TYPE_SALES_SUM);
        if ($orderFieldData) {
            $orderFieldData->update([
                'value' => $formattedResult,
            ]);
        } else {
            $data = [
                'value' => $formattedResult,
                'field_id' => TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_SALES_SUM)->id,
            ];

            $order->data()->create($data);
        }
    }

    protected function getOrderFieldData(Order $order, string $targetField): ?OrderData
    {
        $targetFieldId = TableField::where('type', $targetField)->first()?->id;

        return OrderData::where('order_id', $order->id)->where('field_id', $targetFieldId)->first();
    }
}
