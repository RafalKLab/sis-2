<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\MainController;
use App\Service\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends MainController
{
    public function index(Request $request)
    {
        $targetYear = now()->year;
        $yearsSelect = range($targetYear, $targetYear - 9);

        $selectedYear = $request->selectedYear;

        if ($selectedYear && in_array($selectedYear, $yearsSelect)) {
            $targetYear = $selectedYear;
        }

        $statistics = $this->factory()->createStatisticsManager()->retrieveAnnualStatistics($targetYear);
        $profitAreaChartData = $this->extractDataForProfitAreaChart($statistics);

        $currentMonth = StatisticsService::getMonthName(date('Y-m'));

        return view(
            'main.admin.statistics.index',
            compact('statistics', 'currentMonth', 'profitAreaChartData', 'targetYear', 'yearsSelect'),
        );
    }

    protected function extractDataForProfitAreaChart(array $statistics): array
    {
        $data = [];
        foreach ($statistics as $statistic) {
            $data[$statistic['month_name']] = $statistic['profit']['actual_profit'];
        }

        return $data;
    }
}
