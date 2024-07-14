<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Models\Table\TableField;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function edit(int $id) {
        $targetField = $this->factory()->createTableManagerAdmin()->getField($id);
        if (!$targetField) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'Field not found');
        }

        $tableFields = $this->factory()->createTableManagerAdmin()->retrieveTableFields();

        return view('main.admin.field.edit', compact('tableFields', 'targetField'));
    }

    public function update(Request $request, int $id) {
        $targetField = $this->factory()->createTableManagerAdmin()->getField($id);
        if (!$targetField) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'Field not found');
        }

        $validatedData = $request->validate([
            'color' => 'required',
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('table_fields')->ignore($id),
            ],
        ]);

        $targetField->update($validatedData);

        return redirect()->route('admin-fields.show', ['id'=>$id])->with(ConfigDefaultInterface::FLASH_SUCCESS, 'Field updated successfully');
    }

    public function moveUpFieldOrder(int $id)
    {
        $targetField = $this->factory()->createTableManagerAdmin()->getField($id);
        if (!$targetField) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'Field not found');
        }

        $currentOrder = $targetField->order;
        $targetOrder = $currentOrder - 1;

        // Find field that has target order value
        $fieldWithTargetOrder = TableField::where('order', $targetOrder)->first();

        // Swap orders
        $targetField->update(['order' => $targetOrder]);
        $fieldWithTargetOrder->update(['order' => $currentOrder]);

        return redirect()->route('admin-fields.index')->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Field %s moved up', $targetField->name));
    }

    public function moveDownFieldOrder(int $id)
    {
        $targetField = $this->factory()->createTableManagerAdmin()->getField($id);
        if (!$targetField) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'Field not found');
        }

        $currentOrder = $targetField->order;
        $targetOrder = $currentOrder + 1;

        // Find field that has target order value
        $fieldWithTargetOrder = TableField::where('order', $targetOrder)->first();

        // Swap orders
        $targetField->update(['order' => $targetOrder]);
        $fieldWithTargetOrder->update(['order' => $currentOrder]);

        return redirect()->route('admin-fields.index')->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Field %s moved down', $targetField->name));
    }
}
