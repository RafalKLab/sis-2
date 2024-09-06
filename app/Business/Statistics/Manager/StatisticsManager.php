<?php

namespace App\Business\Statistics\Manager;

use App\Models\Order\Invoice;
use App\Models\Order\Order;
use App\Models\Order\OrderData;
use App\Models\Table\TableField;
use App\Models\User;
use App\Service\InvoiceService;
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

    public function retrieveAnnualStatisticsAllUsersOrSpecificUser(string $targetYear, ?User $user = null): array
    {
        $months = $this->getMonths($targetYear);

        $annualStatistics = $this->calculateAnnualStatisticsUsers($months, $user);
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

    private function calculateAnnualStatisticsUsers(array $months, ?User $user = null): array
    {
        $statistics = [];
        foreach ($months as $month) {
            if ($user) {
                $statistics[$month] = $this->calculateMonthStatisticsForUser($user, $month);
            } else {
                $statistics[$month] = $this->calculateMonthStatisticsUsers($month);
            }
        }

        return $statistics;
    }

    private function calculateMonthStatistics(string $month): array
    {
        $orderIds = $this->getOrdersForMonth($month);

        return [
            'orders' => $this->getOrderKeys($orderIds),
            'profit' => $this->calculateProfit($orderIds),
            'paid_in_advance' => $this->calculatePaidInAdvanceOrders($orderIds),
            'debts' => $this->calculateDebts($orderIds),
            'expenses' => $this->calculateExpenses($orderIds),
        ];
    }
    private function calculateMonthStatisticsUsers(string $month): array
    {
        $monthPattern = $month . '%';
        $orderDateFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_ORDER_DATE)->id;
        $orderIds = OrderData::where('field_id', $orderDateFieldId)
            ->where('value', 'like', $monthPattern)
            ->pluck('order_id')
            ->toArray();

        $orderSaleInvoices = $this->getOrdersWithSalesInvoices($orderIds);

        $ordersByUserId = [];
        $orders = Order::find($orderIds);
        foreach ($orders as $order) {
            // Check if order is paid
            if (!array_key_exists($order->id, $orderSaleInvoices)) {
                continue;
            }

            $invoice = Invoice::where('invoice_number', $orderSaleInvoices[$order->id])->first();
            if ($invoice->status !== ConfigDefaultInterface::INVOICE_STATUS_PAID) {
                continue;
            }

            $ordersByUserId[$order->user_id]['orders'][$order->id] = [
                'order_id' => $order->id,
                'order_key' => $order->getKeyField(),
                'profit' => (float) $this->getOrderFieldDataByType($order->id, ConfigDefaultInterface::FIELD_TYPE_PROFIT)?->value,
            ];
        }

        // Populate data with user info
        $users = User::all();
        foreach ($users as $user) {
            if (array_key_exists($user->id, $ordersByUserId)) {
                $ordersByUserId[$user->id]['user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            } else {
                $ordersByUserId[$user->id]['user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
                $ordersByUserId[$user->id]['orders'] = [];
            }
        }

        // Populate data with additional data
        foreach ($ordersByUserId as &$item) {
            $item['total_orders'] = count($item['orders']);
            $item['profit'] = $this->countUserOrdersProfit($item['orders']);
        }
        unset($item);

        usort($ordersByUserId, function ($item1, $item2) {
            return $item2['profit'] <=> $item1['profit'];
        });

        return [
            'users' => $ordersByUserId,
        ];
    }

    private function calculateMonthStatisticsForUser(User $user, string $month): array
    {
        $monthPattern = $month . '%';
        $orderDateFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_ORDER_DATE)->id;
        $orderIds = OrderData::where('field_id', $orderDateFieldId)
            ->where('value', 'like', $monthPattern)
            ->pluck('order_id')
            ->toArray();

        $orderSaleInvoices = $this->getOrdersWithSalesInvoices($orderIds);

        $ordersByUserId = [];
        $orders = Order::whereIn('id', $orderIds)
            ->where('user_id', $user->id)
            ->get();

        foreach ($orders as $order) {
            // Check if order is paid
            $notPaidOrder = false;
            if (!array_key_exists($order->id, $orderSaleInvoices)) {
                $notPaidOrder = true;
            }

            if (!$notPaidOrder) {
                $invoice = Invoice::where('invoice_number', $orderSaleInvoices[$order->id])->first();
                if ($invoice->status !== ConfigDefaultInterface::INVOICE_STATUS_PAID) {
                    $notPaidOrder = true;
                }
            }

            $profit = (float) $this->getOrderFieldDataByType($order->id, ConfigDefaultInterface::FIELD_TYPE_PROFIT)?->value;

            if ($notPaidOrder) {
                $ordersByUserId[$order->user_id]['not_paid_orders'][$order->id] = [
                    'order_id' => $order->id,
                    'order_key' => $order->getKeyField(),
                    'profit' => $this->formatNumberWithDecimals($profit),
                ];
            } else {
                $ordersByUserId[$order->user_id]['orders'][$order->id] = [
                    'order_id' => $order->id,
                    'order_key' => $order->getKeyField(),
                    'profit' => $this->formatNumberWithDecimals($profit),
                ];
            }
        }

        // Populate data with user info
        if (array_key_exists($user->id, $ordersByUserId)) {
            $ordersByUserId[$user->id]['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];

        } else {
            $ordersByUserId[$user->id]['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
            $ordersByUserId[$user->id]['orders'] = [];
            $ordersByUserId[$user->id]['not_paid_orders'] = [];
        }

        // Populate data with additional data
        foreach ($ordersByUserId as &$item) {
            if (!array_key_exists('orders', $item)) {
                $item['orders'] = [];
            }

            if (!array_key_exists('not_paid_orders', $item)) {
                $item['not_paid_orders'] = [];
            }

            $item['total_orders'] = count($item['orders']);
            $item['total_not_paid_orders'] = count($item['not_paid_orders']);
            $item['profit'] = $this->countUserOrdersProfit($item['orders']);
            $item['expected_profit'] = $this->countUserOrdersProfit($item['not_paid_orders']);;
        }
        unset($item);

        usort($ordersByUserId, function ($item1, $item2) {
            return $item2['profit'] <=> $item1['profit'];
        });

        return [
            'users' => $ordersByUserId,
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
            ->pluck('order_id','value')
            ->toArray();
    }

    private function calculateProfit(array $orderIds): array
    {
        $actualProfit = 0.0;
        $actualProfitDetails = [];
        $expectedProfit = 0.0;
        $expectedProfitDetails = [];

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
            $details = [
                'order_id' => $orderId,
                'order_key' => OrderService::getKeyFieldFrom($orderId)->value,
                'order_sales_sum' => $this->formatNumberWithDecimals($orderProfit),
                'invoice_number' => $invoice->invoice_number,
            ];

            if ($invoice->status === ConfigDefaultInterface::INVOICE_STATUS_PAID) {
                $actualProfit += $orderProfit;
                $actualProfitDetails[] = $details;
            } else {
                $expectedProfit += $orderProfit;
                $expectedProfitDetails[] = $details;
            }
        }

        return [
            'actual_profit' => $this->formatNumberWithDecimals($actualProfit),
            'actual_profit_details' => $actualProfitDetails,
            'expected_profit' => $this->formatNumberWithDecimals($expectedProfit),
            'expected_profit_details' => $expectedProfitDetails,
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

    private function calculatePaidInAdvanceOrders(array $orderIds): array
    {
        $totalPrimeCost = 0.0;
        $details = [];

        $statuses = OrderService::getOrderStatuses($orderIds);
        foreach ($statuses as $orderId => $status) {
            $orderPrimeCost = OrderService::getOrderPrimeCost($orderId);
            $totalPrimeCost += $orderPrimeCost;

            $details[] = [
                'order_id' => $orderId,
                'order_key' => OrderService::getKeyFieldFrom($orderId)->value,
                'prime_cost' => $this->formatNumberWithDecimals($orderPrimeCost),
            ];
        }

        return [
            'total_prime_cost' => $this->formatNumberWithDecimals($totalPrimeCost),
            'details' => $details,
        ];
    }

    private function calculateDebts(array $orderIds): array
    {
        $totalDebts = 0;
        $totalDebtsSum = 0.0;
        $details = [];

        foreach ($orderIds as $orderId) {
            $orderInvoices = InvoiceService::getNotPaidInvoicesForOrder($orderId);

            if (empty($orderInvoices)) {
                continue;
            }

            // Keep only expense invoices
            $orderInvoices = array_filter($orderInvoices, function($invoice) {
                return in_array($invoice['identifier'], ConfigDefaultInterface::EXPENSE_INVOICES);
            });

            $totalDebts += count($orderInvoices);
            $orderDebts = [
                'order_id' => $orderId,
                'order_key' => OrderService::getKeyFieldFrom($orderId)->value,
                'debts' => $orderInvoices
            ];

            $totalDebtsSum += $this->countInvoiceSum($orderDebts['debts']);
            $details[] = $orderDebts;

        }

        return [
            'total_debts' => $totalDebts,
            'total_debts_sum' => $this->formatNumberWithDecimals($totalDebtsSum),
            'details' => $details,
        ];
    }

    private function calculateExpenses(array $orderIds): array
    {
        $totalExpenses = 0.0;
        $details = [];

        foreach ($orderIds as $orderId) {
            $orderInvoices = InvoiceService::getPaidInvoicesForOrder($orderId);

            if (empty($orderInvoices)) {
                continue;
            }

            // Keep only expense invoices
            $orderInvoices = array_filter($orderInvoices, function($invoice) {
                return in_array($invoice['identifier'], ConfigDefaultInterface::EXPENSE_INVOICES);
            });

            $orderDebts = [
                'order_id' => $orderId,
                'order_key' => OrderService::getKeyFieldFrom($orderId)->value,
                'expenses' => $orderInvoices
            ];

            $totalExpenses += $this->countInvoiceSum($orderDebts['expenses']);
            $details[] = $orderDebts;

        }

        return [
            'total_expenses' => $this->formatNumberWithDecimals($totalExpenses),
            'details' => $details,
        ];
    }

    private function formatNumberWithDecimals($number): string
    {
        return number_format($number, 2, '.', '');
    }

    private function getOrderFieldDataByType(int $orderId, string $targetField): ?OrderData
    {
        $targetFieldId = TableField::where('type', $targetField)->first()?->id;

        return OrderData::where('order_id', $orderId)->where('field_id', $targetFieldId)->first();
    }

    private function countUserOrdersProfit(array $orders): string
    {
        $profit = 0.00;
        foreach ($orders as $order) {
            $profit += $order['profit'];
        }

        return $this->formatNumberWithDecimals($profit);
    }

    private function countInvoiceSum(array $invoices): float
    {
        $sum = 0.0;
        foreach ($invoices as $invoice) {
            $sum += $invoice['sum'];
        }

        return $sum;
    }
}
