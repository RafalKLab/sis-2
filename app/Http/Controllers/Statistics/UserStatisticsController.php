<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\MainController;
use App\Service\StatisticsService;
use Illuminate\Http\Request;

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

        $statistics = $this->factory()->createStatisticsManager()->retrieveAnnualStatisticsAllUsers($targetYear);
        $currentMonth = StatisticsService::getMonthName(date('Y-m'));

        $performanceChartData = $this->exteractDataForPerformanceChart($statistics);

        return view(
            'main.admin.statisticsUser.index',
            compact('currentMonth','statistics', 'targetYear', 'yearsSelect', 'performanceChartData'),
        );
    }

    public function show(int $userId, string $year, string $month) {
        dump($userId);
        dump($year);
        dump($month);

        dd('in progress');
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
}
