<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\MainController;
use App\Models\Company\Company;
use App\Service\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends MainController
{
    public function index(Request $request)
    {
        $targetYear = now()->year;
        $yearsSelect = range($targetYear, $targetYear - 9);

        $targetCompany = Company::first()->toArray();
        $companySelect = Company::all()->toArray();

        if (empty($targetCompany)) {
            $targetCompany = [
                'name' => 'undefined',
                'id' => 0,
            ];
        }

        $selectedYear = $request->selectedYear;
        $selectedCompany = $request->selectedCompany;

        if ($selectedYear && in_array($selectedYear, $yearsSelect)) {
            $targetYear = $selectedYear;
        }

        $selectedCompanyEntity = Company::find($selectedCompany);
        if ($selectedCompany && $selectedCompanyEntity) {
            $targetCompany = $selectedCompanyEntity->toArray();
        }

        $targetCompanyId = $targetCompany['id'];

        $statistics = $this->factory()->createStatisticsManager()->retrieveAnnualStatistics($targetYear, $targetCompanyId);
        $profitAreaChartData = $this->extractDataForProfitAreaChart($statistics);
        $currentMonth = StatisticsService::getMonthName(date('Y-m'));

        return view(
            'main.admin.statistics.index',
            compact('statistics', 'currentMonth', 'profitAreaChartData', 'targetYear', 'yearsSelect', 'targetCompany', 'companySelect'),
        );
    }

    protected function extractDataForProfitAreaChart(array $statistics): array
    {
        $actualProfit = [];
        $expectedProfit = [];
        foreach ($statistics as $statistic) {
            $actualProfit[$statistic['month_name']] = $statistic['profit']['actual_profit'];
            $expectedProfit[$statistic['month_name']] = $this->calculateExpectedProfitForChart($statistic);
        }

        return [
            'actual' => $actualProfit,
            'expected' => $expectedProfit,
        ];
    }

    private function calculateExpectedProfitForChart(array $statistic): string
    {
        $expectedProfit = $statistic['profit']['actual_profit'] + $statistic['profit']['expected_profit'];

        return number_format($expectedProfit, 2, '.', '');
    }
}
