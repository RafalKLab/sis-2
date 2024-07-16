<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class OrderController extends MainController
{
    public function index(Request $request)
    {
        $search = $request->search;
        $tableData = $this->factory()->createTableManager()->retrieveTableData($search);

        return view('main.user.order.index', compact('tableData', 'search'));
    }
}
