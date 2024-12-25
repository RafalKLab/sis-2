<?php

namespace App\Business\Goal\Manager;

use App\Models\Goal\Goal;
use App\Models\Order\Invoice;
use App\Models\Order\OrderData;
use App\Service\TableService;
use shared\ConfigDefaultInterface;

class GoalManager
{
    public function getGoals(): array
    {
        $goals = Goal::orderBy('created_at', 'DESC')->where('is_visible', true)->get()->toArray();

        foreach ($goals as &$goal) {
            $sales = $this->calculateOrderSales($goal['start_date']);

            $salesPercentage = $sales * 100 / $goal['amount'];

            $salesPercentage = (floor($salesPercentage) == $salesPercentage)
                ? (int) $salesPercentage
                : number_format($salesPercentage, 2);

            $leftSales = $goal['amount'] - $sales;

            $leftPercentage = 100 - (int) $salesPercentage;

            if ($sales > $goal['amount']) {
                $leftSales = 0;
                $leftPercentage = 0;
            }

            $goal['amount'] = number_format($goal['amount'], 0, '.', ' ');
            $goal['sales'] = number_format($sales, 0, '.', ' ');
            $goal['sales_percentage'] = $salesPercentage;
            $goal['left_sales'] = number_format($leftSales, 0, '.', ' ');
            $goal['left_percentage'] = $leftPercentage;
        }

        return $goals;
    }

    private function calculateOrderSales(string $startDate): float
    {
        $fieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_ORDER_DATE)->id;

        $orderIds = OrderData::where('field_id', $fieldId)
            ->where('value', '>=', $startDate)
            ->pluck('order_id')
            ->toArray();

        return (float) Invoice::where('status', ConfigDefaultInterface::INVOICE_STATUS_PAID)
            ->whereIn('order_id', $orderIds)
            ->whereNotNull('customer')
            ->where('is_trans', false)
            ->sum('sum');
    }
}
