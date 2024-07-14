<?php

namespace App\Http\Controllers\Admin;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Http\Controllers\MainController;
use App\Models\Logs\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityLogController extends MainController
{
    public function index(Request $request)
    {
        // Retrieve date parameters from the query string
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        if (!$fromDate || !$toDate) {
            $fromDate = Carbon::now()->startOfMonth();
            $toDate = Carbon::now()->endOfMonth();
        }

        $dateRange = $this->formatDateRange($fromDate, $toDate);

        $logsQuery = ActivityLog::whereBetween('created_at', [$fromDate, $toDate]);

        // Clone the base query for different log types
        $infoLogs = (clone $logsQuery)->where('title', ActivityLogConstants::INFO_LOG)->get();
        $warningLogs = (clone $logsQuery)->where('title', ActivityLogConstants::WARNING_LOG)->get();
        $dangerLogs = (clone $logsQuery)->where('title', ActivityLogConstants::DANGER_LOG)->get();

        return view(
            'main.admin.activityLogs.index',
            compact('infoLogs', 'warningLogs', 'dangerLogs', 'dateRange')
        );
    }

    private function formatDateRange(array|Carbon|string|null $fromDate, array|Carbon|string|null $toDate): string
    {
        $fromDateFormated = Carbon::parse($fromDate)->format('Y-m-d');
        $toDateFormated = Carbon::parse($toDate)->format('Y-m-d');

        return $fromDateFormated . ' to ' . $toDateFormated;
    }
}
