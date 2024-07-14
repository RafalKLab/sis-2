<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;

class AdminTableController extends MainController
{
    public function index(Request $request)
    {
        $search = $request->search;
        $tableData = $this->factory()->createTableManagerAdmin()->retrieveTableData($search);

        return view('main.admin.table.index', compact('tableData', 'search'));
    }
}
