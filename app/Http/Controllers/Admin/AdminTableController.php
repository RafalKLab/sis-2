<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;

class AdminTableController extends MainController
{
    public function index()
    {
        $tableData = $this->factory()->createTableManagerAdmin()->retrieveTableData();

        return view('main.admin.table.index', compact('tableData'));
    }
}
