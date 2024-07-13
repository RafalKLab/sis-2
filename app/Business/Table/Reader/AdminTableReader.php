<?php

namespace App\Business\Table\Reader;

use App\Business\Table\Config\TableConfig;
use App\Models\Table\Table;

class AdminTableReader implements TableReaderInterface
{
    public function readTableData(): array
    {
        $mainTable = $this->findMainTable();
        if (!$mainTable) {
            return [];
        }

        $fieldData = $this->findTableFields($mainTable);

        return [
            'name' => $mainTable->name,
            'fields' => $fieldData,
            'data' => [],
        ];
    }

    private function findMainTable(): ?Table
    {
        return Table::where('name', TableConfig::MAIN_TABLE_NAME)->first();
    }

    private function findTableFields(Table $mainTable): array
    {
        $fieldData =[];
        foreach ($mainTable->fields as $field) {
            $fieldData[] = [
                'name' => $field->name,
                'type' => $field->type,
                'color' => $field->color,
            ];
        }

        return $fieldData;
    }
}
