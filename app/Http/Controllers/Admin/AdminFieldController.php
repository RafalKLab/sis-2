<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use shared\ConfigDefaultInterface;

class AdminFieldController extends MainController
{
    public function index()
    {
        $tableFields = $this->factory()->createTableManagerAdmin()->retrieveTableFields();

        return view('main.admin.field.index', compact('tableFields'));
    }

    public function show(int $id) {
        $targetField = $this->factory()->createTableManagerAdmin()->getField($id);
        if (!$targetField) {
            return redirect()->route('admin-fields.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Field not found');
        }

        $tableFields = $this->factory()->createTableManagerAdmin()->retrieveTableFields();

        return view('main.admin.field.show', compact('tableFields', 'targetField'));
    }
}
