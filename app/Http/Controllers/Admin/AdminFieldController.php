<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Models\Table\Table;
use App\Models\Table\TableField;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use shared\ConfigDefaultInterface;

class AdminFieldController extends MainController
{
    protected const TABLE_CONTEXT = 'TABLE_CONTEXT';

    public function index(Request $request)
    {
        $tableContext = $request->input('table_context');
        if ($tableContext) {
            Session::put(self::TABLE_CONTEXT, $tableContext);
        }

        $table = $this->getTableFromContext();
        $selectedTable = $table->name;
        $availableTables = Table::all()->pluck('name')->toArray();
        $tableFields = $this->factory()->createTableManagerAdmin()->retrieveTableFields($table);

        return view('main.admin.field.index', compact('tableFields', 'availableTables', 'selectedTable'));
    }

    public function show(int $id) {
        $availableTables = Table::all()->pluck('name')->toArray();
        $table = $this->getTableFromContext();
        $selectedTable = $table->name;

        $targetField = $this->factory()->createTableManagerAdmin()->getField($id);
        if (!$targetField) {
            return redirect()->route('admin-fields.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Field not found');
        }

        $tableFields = $this->factory()->createTableManagerAdmin()->retrieveTableFields($table);

        return view('main.admin.field.show', compact('tableFields', 'targetField', 'availableTables', 'selectedTable'));
    }

    public function edit(int $id) {
        $availableTables = Table::all()->pluck('name')->toArray();
        $table = $this->getTableFromContext();
        $selectedTable = $table->name;

        $targetField = $this->factory()->createTableManagerAdmin()->getField($id);
        if (!$targetField) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'Field not found');
        }

        $tableFields = $this->factory()->createTableManagerAdmin()->retrieveTableFields($table);
        $fieldGroups = ConfigDefaultInterface::AVAILABLE_FIELD_GROUPS;

        return view('main.admin.field.edit', compact('tableFields', 'targetField', 'fieldGroups', 'availableTables', 'selectedTable'));
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
            'group' => [
                'required',
                'string',
                'in:' . implode(',', ConfigDefaultInterface::AVAILABLE_FIELD_GROUPS)
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
        $table = $this->getTableFromContext();

        $fieldWithTargetOrder = TableField::where('order', $targetOrder)->where('table_id', $table->id)->first();

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
        $table = $this->getTableFromContext();

        $fieldWithTargetOrder = TableField::where('order', $targetOrder)->where('table_id', $table->id)->first();

        // Swap orders
        $targetField->update(['order' => $targetOrder]);
        $fieldWithTargetOrder->update(['order' => $currentOrder]);

        return redirect()->route('admin-fields.index')->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Field %s moved down', $targetField->name));
    }

    public function create() {
        $availableTables = Table::all()->pluck('name')->toArray();
        $table = $this->getTableFromContext();
        $selectedTable = $table->name;

        $tableFields = $this->factory()->createTableManagerAdmin()->retrieveTableFields($table);
        $fieldTypes = ConfigDefaultInterface::AVAILABLE_FIELD_TYPES;
        $fieldGroups = ConfigDefaultInterface::AVAILABLE_FIELD_GROUPS;

        return view('main.admin.field.create', compact('tableFields', 'fieldTypes', 'fieldGroups', 'availableTables', 'selectedTable'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => [
                'required',
                'string',
                'in:' . implode(',', ConfigDefaultInterface::AVAILABLE_FIELD_TYPES)
            ],
            'group' => [
                'required',
                'string',
                'in:' . implode(',', ConfigDefaultInterface::AVAILABLE_FIELD_GROUPS)
            ],
            'color' => 'required'
        ]);

        $tableId = $this->getTableFromContext()->id;
        $order = TableField::where('table_id', $tableId)->count() + 1;

        $field = TableField::create([
            'order' => $order,
            'table_id' => $tableId,
            'color' => $validated['color'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'group' => $validated['group']
        ]);

        return redirect()->route('admin-fields.edit', ['id' => $field->id])
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, 'Field created');
    }

    private function getTableFromContext(): Table
    {
        $tableName = Session::get(self::TABLE_CONTEXT);

        if ($tableName) {
            $table = Table::where('name', $tableName)->first();
            if ($table) {
                return $table;
            }
        }

        return Table::first();
    }
}
