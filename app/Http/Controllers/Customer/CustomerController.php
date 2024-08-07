<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Auth;
use shared\ConfigDefaultInterface;

class CustomerController extends MainController
{
    public function index()
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_ACCESS_CUSTOMER_TABLE)) {
            return redirect()->route('dashboard')->with(ConfigDefaultInterface::FLASH_ERROR, ConfigDefaultInterface::ERROR_MISSING_PERMISSION);
        }

        $customersData = $this->factory()->createCustomerManager()->getCustomersData();

        return view('main.user.customer.index', compact('customersData'));
    }
}
