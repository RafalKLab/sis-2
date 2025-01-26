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
            $salesData = $this->calculateOrderSales($goal['start_date']);
            $sales = $salesData['sales'];

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
            $goal['sales_data'] = $salesData;
        }

        return $goals;
    }

    private function calculateOrderSales(string $startDate): array
    {
        $invoices = Invoice::where('issue_date', '>=', $startDate)
            ->whereNotNull('customer')
            ->where('is_trans', false)
            ->get();

        $sum = 0.0;
        $invoiceData = [];
        foreach ($invoices as $invoice) {
            $sum += (float) $invoice->sum;
            $invoiceData[] = [
                'number' => $invoice->invoice_number,
                'issue_date' => $invoice->issue_date,
                'sum' => $invoice->sum,
                'buyer' => $invoice->customer,
                'order_id' => $invoice->order_id,
                'order_key' => $invoice->order->getKeyField(),
            ];
        }

        return [
            'sales' => $sum,
            'invoice_data' => $invoiceData,
        ];
    }
}
