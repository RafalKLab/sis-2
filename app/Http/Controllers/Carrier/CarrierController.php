<?php

namespace App\Http\Controllers\Carrier;

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Auth;
use shared\ConfigDefaultInterface;

class CarrierController extends MainController
{
    public function index()
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_ACCESS_CARRIER_TABLE)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $carriers = $this->factory()->createCarrierManager()->getCarriersData();

        return view('main.user.carrier.index', compact('carriers'));
    }
}
