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
use Dflydev\DotAccessData\Data;
use shared\ConfigDefaultInterface;

class StatisticsManager
{
    protected const IS_ACTUAL_PROFIT = 'is_actual_profit';
    protected const BUYER_INVOICES = 'buyer_invoices';
    protected const OBLIGATION_INVOICES = 'obligation_invoices';

    public function retrieveAnnualStatistics(string $targetYear, int $companyId): array
    {
        $months = $this->getMonths($targetYear);

        $annualStatistics = $this->calculateAnnualStatistics($months, $companyId);
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

    private function calculateAnnualStatistics(array $months, int $companyId): array
    {
        $statistics = [];
        foreach ($months as $month) {
            $statistics[$month] = $this->calculateMonthStatistics($month, $companyId);
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

    private function calculateMonthStatistics(string $month, int $companyId): array
    {
        $orderIds = $this->getOrdersForMonth($month, $companyId);
        $monthInvoices = $this->getInvoicesForMonth($month, $companyId);

        return [
            'orders' => $this->getOrderKeys($orderIds),
            'profit' => $this->calculateProfit($monthInvoices),
            'paid_in_advance' => $this->calculatePaidInAdvanceOrders($orderIds),
            'debts' => $this->calculateDebts($monthInvoices),
            'expenses' => $this->calculateExpenses($monthInvoices),
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

        // TODO Implement with invoice issue date
        $orderBuyerInvoices = $this->getOrdersProfitStatusByBuyersInvoices($orderIds);

        $ordersByUserId = [];
        $orders = Order::find($orderIds);
        foreach ($orders as $order) {
            // Check if order is paid
            if (!array_key_exists($order->id, $orderBuyerInvoices)) {
                continue;
            }

            if (!$orderBuyerInvoices[$order->id]) {
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

        // TODO Implement with invoice issue date
        $orderBuyerInvoices = $this->getOrdersProfitStatusByBuyersInvoices($orderIds);

        $ordersByUserId = [];
        $orders = Order::whereIn('id', $orderIds)
            ->where('user_id', $user->id)
            ->get();

        foreach ($orders as $order) {
            // Check if order is paid
            $notPaidOrder = false;
            if (!array_key_exists($order->id, $orderBuyerInvoices)) {
                $notPaidOrder = true;
            }

            if (!$notPaidOrder) {
                if (!$orderBuyerInvoices[$order->id]) {
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

    private function getOrdersForMonth(string $month, int $companyId): array
    {
        $orderDateFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_ORDER_DATE)->id;
        $monthPattern = $month . '%'; // Append a '%' wildcard to the month string

        return OrderData::where('field_id', $orderDateFieldId)
            ->where('value', 'LIKE', $monthPattern)
            ->whereHas('order', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
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

    private function calculateProfit(array $monthInvoices): array
    {
        $actualProfit = 0.0;
        $actualProfitDetails = [];
        $expectedProfit = 0.0;
        $expectedProfitDetails = [];

        foreach ($monthInvoices[static::BUYER_INVOICES] as $orderId => $invoices) {

            // find order sales value
            $orderTotalProfitFieldId = TableService::getFieldByType(ConfigDefaultInterface::FIELD_TYPE_PROFIT)->id;
            $orderProfit = (float) OrderData::where('order_id', $orderId)
                ->where('field_id', $orderTotalProfitFieldId)
                ->first()
                ->value;

            $details = [
                'order_id' => $orderId,
                'order_key' => OrderService::getKeyFieldFrom($orderId)->value,
                'order_sales_sum' => $this->formatNumberWithDecimals($orderProfit),
            ];

            if ($monthInvoices[static::BUYER_INVOICES][$orderId][static::IS_ACTUAL_PROFIT]) {
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

    private function calculateDebts(array $monthInvoices): array
    {
        $totalDebts = 0;
        $totalDebtsSum = 0.0;
        $details = [];

        foreach ($monthInvoices[static::OBLIGATION_INVOICES] as $orderId => $invoices) {
            $debtInvoices = array_filter($invoices, function ($invoice) {
                return $invoice['status'] !== ConfigDefaultInterface::INVOICE_STATUS_PAID;
            });

            $debtInvoices = $this->expandObligationInvoices($debtInvoices);
            $debtInvoices = array_filter($debtInvoices, function($invoice) {
                return in_array($invoice['identifier'], ConfigDefaultInterface::EXPENSE_INVOICES);
            });

            $totalDebts += count($debtInvoices);
            $orderDebts = [
                'order_id' => $orderId,
                'order_key' => OrderService::getKeyFieldFrom($orderId)->value,
                'debts' => $debtInvoices
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

    private function calculateExpenses(array $monthInvoices): array
    {
        $totalExpenses = 0.0;
        $details = [];

        foreach ($monthInvoices[static::OBLIGATION_INVOICES] as $orderId => $invoices) {
            $debtInvoices = array_filter($invoices, function ($invoice) {
                return $invoice['status'] === ConfigDefaultInterface::INVOICE_STATUS_PAID;
            });

            $debtInvoices = $this->expandObligationInvoices($debtInvoices);
            $debtInvoices = array_filter($debtInvoices, function($invoice) {
                return in_array($invoice['identifier'], ConfigDefaultInterface::EXPENSE_INVOICES);
            });

            $orderDebts = [
                'order_id' => $orderId,
                'order_key' => OrderService::getKeyFieldFrom($orderId)->value,
                'expenses' => $debtInvoices
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

    private function getOrdersProfitStatusByBuyersInvoices(array $orderIds): array
    {
        // checks order buyer invoices if all invoices are paid than 1 else 0
        $ordersProfitStatus = [];

        foreach ($orderIds as $orderId) {
            $orderBuyerInvoices = Invoice::where('order_id', $orderId)->whereNotNull('customer')->where('is_trans', false)->get();

            if (count($orderBuyerInvoices) == 0) {
                continue;
            }

            $status = 1;
            foreach ($orderBuyerInvoices as $buyerInvoice) {
                if ($buyerInvoice->status !== ConfigDefaultInterface::INVOICE_STATUS_PAID) {
                    $status = 0;
                }
            }

            $ordersProfitStatus[$orderId] = $status;
        }

        return $ordersProfitStatus;
    }

    private function getInvoicesForMonth(string $month, int $companyId): array
    {
        $monthPattern = $month . '%';

        // Fetch buyer invoices with necessary data and relationships
        $buyerInvoices = Invoice::where('is_trans', false)
            ->whereNotNull('customer')
            ->where('issue_date', 'LIKE', $monthPattern)
            ->whereHas('order', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->with(['order'])
            ->get()
            ->groupBy('order_id');

        $invoicesByOrder = $buyerInvoices->map(function ($invoices) {
            $allPaid = $invoices->every(function ($invoice) {
                return $invoice->status === ConfigDefaultInterface::INVOICE_STATUS_PAID;
            });

            return [
                'invoices' => $invoices->map(fn($invoice) => ['status' => $invoice->status])->toArray(),
                static::IS_ACTUAL_PROFIT => $allPaid,
            ];
        });

        // Obligations (expenses/debts)
        $obligations = Invoice::where('is_trans', false)
            ->where('customer', null)
            ->where('issue_date', 'LIKE', $monthPattern)
            ->whereHas('order', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->with(['order'])
            ->get()
            ->groupBy('order_id');


        return [
            static::BUYER_INVOICES => $invoicesByOrder->toArray(),
            static::OBLIGATION_INVOICES => $obligations->toArray(),
        ];
    }

    private function expandObligationInvoices(array $debtInvoices): array
    {
        $invoices = [];
        foreach ($debtInvoices as $debtInvoice) {
            $field = $debtInvoice['field_id'] ? TableService::getFieldById($debtInvoice['field_id']) : null;

            $invoices[] = [
                'invoice_name' => $field->name ?? '',
                'invoice_number' => $debtInvoice['invoice_number'],
                'sum' => number_format($debtInvoice['sum'], 2, '.', ''),
                'identifier' => $field->identifier ?? '',
            ];
        }

        return $invoices;
    }

}
