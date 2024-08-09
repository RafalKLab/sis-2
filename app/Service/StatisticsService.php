<?php

namespace App\Service;

class StatisticsService
{
    protected const MONTH_NAMES = [
        '01' => 'Sausis',
        '02' => 'Vasaris',
        '03' => 'Kovas',
        '04' => 'Balandis',
        '05' => 'Gegužė',
        '06' => 'Birželis',
        '07' => 'Liepa',
        '08' => 'Rugpjūtis',
        '09' => 'Rugsėjis',
        '10' => 'Spalis',
        '11' => 'Lapkritis',
        '12' => 'Gruodis'
    ];

    public static function getMonthName(string $yearMonth): string
    {
        $month = substr($yearMonth, -2); // Extract the last two digits for the month

        return self::MONTH_NAMES[$month] ?? '';
    }
}
