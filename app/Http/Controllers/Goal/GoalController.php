<?php

namespace App\Http\Controllers\Goal;

use App\Http\Controllers\MainController;
use App\Models\Goal\Goal;
use App\Models\Order\Invoice;
use App\Models\Order\OrderData;
use App\Service\TableService;
use Illuminate\Http\Request;
use shared\ConfigDefaultInterface;

class GoalController extends MainController
{
    public function index()
    {
        $goals = Goal::orderBy('created_at', 'DESC')->get()->toArray();
        $goals = $this->calculatePercentages($goals);

        return view('main.admin.goal.index', compact('goals'));
    }

    public function add()
    {
        return view('main.admin.goal.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'amount' => 'required|integer|min:1|max:2147483647',
            'date' =>  'required | date'
        ]);

        Goal::create([
            'start_date' => $validated['date'],
            'name' => $validated['name'],
            'amount' => $validated['amount'],
        ]);

        return redirect()->route('goals.index');
    }

    private function calculatePercentages(array $goals): array
    {
        foreach ($goals as &$goal) {
            $sales = $this->calculateOrderSales($goal['start_date']);

            $salesPercentage = $sales * 100 / $goal['amount'];

            $salesPercentage = (floor($salesPercentage) == $salesPercentage)
                ? (int) $salesPercentage
                : number_format($salesPercentage, 2);

            $leftSales = $goal['amount'] - $sales;
            $leftPercentage = 100 - $salesPercentage;

            if ($sales > $goal['amount']) {
                $leftSales = 0;
                $leftPercentage = 0;
            }

            $goal['amount'] = number_format($goal['amount'], 0, '.', ' ');
            $goal['sales'] = number_format($sales, 0, '.', ' ');
            $goal['sales_percentage'] = $salesPercentage;
            $goal['left_sales'] = number_format($leftSales, 0, '.', ' ');
            $goal['left_percentage'] = $leftPercentage;
        }

        return $goals;
    }

    private function calculateOrderSales(string $startDate): float
    {
        $fieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_ORDER_DATE)->id;

        $orderIds = OrderData::where('field_id', $fieldId)
            ->where('value', '>=', $startDate)
            ->pluck('order_id')
            ->toArray();

        return (float) Invoice::where('status', ConfigDefaultInterface::INVOICE_STATUS_PAID)
            ->whereIn('order_id', $orderIds)
            ->whereNotNull('customer')
            ->sum('sum');
    }
}
