<?php

namespace App\Http\Controllers\FieldSettings;

use App\Http\Controllers\MainController;
use App\Models\Order\Order;
use App\Models\Table\FieldSettings;
use Illuminate\Http\Request;
use shared\ConfigDefaultInterface;

class FieldSettingsController extends MainController
{
    public function toggleFieldAutoCalculations(Request $request)
    {
        $toggleValue = $request->input('toggle');
        $fieldId = $request->input('fieldId');
        $orderId = $request->input('orderId');

        $fieldSetting = FieldSettings::where('field_id', $fieldId)->where('order_id', $orderId)->where('setting', ConfigDefaultInterface::AUTO_CALCULATION_SETTING)->first();

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'error' => 'Order not found'
            ], 404);
        }

        if (!$fieldSetting) {
            FieldSettings::create([
                'field_id' => $fieldId,
                'order_id' => $orderId,
                'setting' => ConfigDefaultInterface::AUTO_CALCULATION_SETTING,
                'value' => $toggleValue,
            ]);
        } else {
            $fieldSetting->update([
                'value' => $toggleValue,
            ]);
        }

        $calculator = $this->factory()->createOrderDataCalculator();
        $calculator->calculateTotalPurchaseSum($order);
        $calculator->calculateDuty7($order);
        $calculator->calculateDuty15($order);
        $calculator->calculatePrimeCost($order);
        $calculator->calculateTotalSalesSum($order);
        $calculator->calculateTotalProfit($order);

        return response()->json(['msg' => 'Success']);
    }
}
