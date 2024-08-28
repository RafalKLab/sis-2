<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\MainController;
use App\Models\User;
use App\Service\StatisticsService;
use Illuminate\Http\Request;
use shared\ConfigDefaultInterface;

class UserStatisticsController extends MainController
{
    public function index(Request $request)
    {
        $targetYear = now()->year;
        $yearsSelect = range($targetYear, $targetYear - 9);

        $selectedYear = $request->selectedYear;

        if ($selectedYear && in_array($selectedYear, $yearsSelect)) {
            $targetYear = $selectedYear;
        }

        $statistics = $this->factory()->createStatisticsManager()->retrieveAnnualStatisticsAllUsersOrSpecificUser($targetYear);
        $currentMonth = StatisticsService::getMonthName(date('Y-m'));

        $performanceChartData = $this->exteractDataForPerformanceChart($statistics);

        return view(
            'main.admin.statisticsUser.index',
            compact('currentMonth','statistics', 'targetYear', 'yearsSelect', 'performanceChartData'),
        );
    }

    public function show(Request $request, int $userId, string $year, string $month) {
        $user = User::find($userId);
        if (!$user) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'User not found!');
        }

        $targetYear = $year;
        $yearsSelect = range($targetYear, $targetYear - 9);

        $selectedYear = $request->selectedYear;

        if ($selectedYear && in_array($selectedYear, $yearsSelect)) {
            $targetYear = $selectedYear;
        }

        $statistics = $this->factory()->createStatisticsManager()->retrieveAnnualStatisticsAllUsersOrSpecificUser($targetYear, $user);
        $currentMonth = StatisticsService::getMonthName(date('Y-m'));

        $yearData = $this->countYearData($statistics);
        $totalYearProfit = $yearData['profit'];
        $totalYearOrders = $yearData['total_orders'];
        $userPerformanceChartData = $this->extractDataForUserPerformanceChart($statistics);

        return view(
            'main.admin.statisticsUser.show',
            compact('currentMonth','statistics', 'targetYear', 'yearsSelect', 'user', 'totalYearProfit', 'userPerformanceChartData', 'totalYearOrders'),
        );
    }

    private function exteractDataForPerformanceChart(array $statistics): array
    {
        $userProfits = [];

        foreach ($statistics as $month) {
            foreach ($month['users'] as $user) {
                (float) $profit = $user['profit'];
                $userName = $user['user']['name'];
                if (array_key_exists($userName, $userProfits)) {
                    $userProfits[$userName] += $profit;
                } else {
                    $userProfits[$userName] = $profit;
                }
            }
        }

        return $userProfits;
    }

    private function countYearData(array $statistics): array
    {
        $totalOrders = 0;
        $profit = 0.00;

        foreach ($statistics as $month) {
            $profit += $month['users'][0]['profit'];
            $totalOrders += $month['users'][0]['total_orders'] + $month['users'][0]['total_not_paid_orders'];
        }

        return [
            'profit' => number_format($profit, 2, '.', ''),
            'total_orders' => $totalOrders,
        ];
    }

    private function extractDataForUserPerformanceChart(array $statistics): array
    {
        $data = [];
        foreach ($statistics as $month) {
            $data[$month['month_name']] = $month['users'][0]['profit'];
        }

        return $data;
    }
}
