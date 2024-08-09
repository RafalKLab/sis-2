<?php

namespace App\Business\Statistics\Manager;

use App\Models\Order\Invoice;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Service\OrderService;
use App\Service\StatisticsService;
use App\Service\TableService;
use DateInterval;
use DatePeriod;
use DateTime;
use shared\ConfigDefaultInterface;

class StatisticsManager
{

    public function retrieveAnnualStatistics(string $targetYear): array
    {
        $months = $this->getMonths($targetYear);

        $annualStatistics = $this->calculateAnnualStatistics($months);
        $annualStatistics = $this->addMonthNames($annualStatistics);

        return $annualStatistics;
    }

    private function getMonths(string $targetYear): array
    {
        $start = new DateTime("{$targetYear}-01-01"); // Start from January 1st of the given year
        $end = new DateTime("{$targetYear}-12-01"); // Go until December 1st of the given year

        $interval = new DateInterval('P1M'); // Set interval as 1 month
        $period = new DatePeriod($start, $interval, $end);

        $months = [];
        foreach ($period as $date) {
            $months[] = $date->format('Y-m'); // Format each month as 'YYYY-MM'
        }
        $months[] = $end->format('Y-m');

        return $months;
    }

    private function calculateAnnualStatistics(array $months): array
    {
        $statistics = [];
        foreach ($months as $month) {
            $statistics[$month] = $this->calculateMonthStatistics($month);
        }

        return $statistics;
    }

    private function calculateMonthStatistics(string $month): array
    {
        $orderIds = $this->getOrdersForMonth($month);

        return [
            'orders' => $this->getOrderKeys($orderIds),
            'profit' => $this->calculateProfit($orderIds),
            'paid_in_advance' => $this->getPaidInAdvanceOrders($orderIds),
        ];
    }

    private function getOrdersForMonth(string $month): array
    {
        $orderDateFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_ORDER_DATE)->id;
        $monthPattern = $month . '%'; // Append a '%' wildcard to the month string

        return OrderData::where('field_id', $orderDateFieldId)
            ->where('value', 'LIKE', $monthPattern)
            ->pluck('order_id')
            ->toArray();
    }

    private function getOrderKeys(array $orderIds): array
    {
        $orderKeyFieldId = OrderService::getKeyField()->id;

        return OrderData::where('field_id', $orderKeyFieldId)
            ->whereIn('order_id', $orderIds)
            ->pluck('value')
            ->toArray();
    }

    private function calculateProfit(array $orderIds): array
    {
        $actualProfit = 0.0;
        $expectedProfit = 0.0;

        $orderSalesInvoices = $this->getOrdersWithSalesInvoices($orderIds);
        foreach ($orderIds as $orderId) {
            if (!array_key_exists($orderId, $orderSalesInvoices)) {
                continue;
            }

            // find order sales value
            $orderTotalProfitFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PROFIT)->id;
            $orderProfit = (float) OrderData::where('order_id', $orderId)
                ->where('field_id', $orderTotalProfitFieldId)
                ->first()
                ->value;

            // find order invoice entity to check status
            $invoice = Invoice::where('invoice_number', $orderSalesInvoices[$orderId])->first();
            if ($invoice->status === ConfigDefaultInterface::INVOICE_STATUS_PAID) {
                $actualProfit += $orderProfit;
            } else {
                $expectedProfit += $orderProfit;
            }
        }

        return [
            'actual_profit' => number_format($actualProfit, 2, '.', ''),
            'expected_profit' => number_format($expectedProfit, 2, '.', ''),
        ];
    }

    private function getOrdersWithSalesInvoices(array $orderIds): array
    {
        $salesInvoiceFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_SALES_INVOICE)->id;

        return OrderData::whereIn('order_id', $orderIds)
            ->where('field_id', $salesInvoiceFieldId)
            ->pluck('value', 'order_id')
            ->toArray();
    }

    private function addMonthNames(array $annualStatistics): array
    {
        foreach ($annualStatistics as $month => &$statistic) {
            $statistic['month_name'] = StatisticsService::getMonthName($month);
        }

        return $annualStatistics;
    }

    private function getPaidInAdvanceOrders(array $orderIds): array
    {
        return [];
    }
}
