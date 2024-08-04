<?php

namespace App\Business\Table\Manager;

use App\Business\Table\Config\TableConfigInterface;
use App\Business\Table\Reader\TableReaderInterface;
use App\Models\Table\Table;
use App\Models\Table\TableField;

class TableManager
{
    protected TableConfigInterface $config;

    protected TableReaderInterface $reader;

    /**
     * @param TableConfigInterface $config
     * @param TableReaderInterface $reader
     */
    public function __construct(TableConfigInterface $config, TableReaderInterface $reader)
    {
        $this->config = $config;
        $this->reader = $reader;
    }

    public function createInitTable(): void
    {
        $table = $this->getOrCreateTable();
        $this->createTableFields($table);
    }

    public function retrieveTableData(?string $search = null): array
    {
        return $this->reader->readTableData($search);
    }

    public function retrieveTableFields(?Table $table = null): array
    {
        return $this->reader->readTableFields($table);
    }

    public function getField(int $id): ?TableField
    {
        return $this->reader->getTableField($id);
    }

    public function getMainTable(): ?Table
    {
        return $this->reader->findMainTable();
    }

    private function getOrCreateTable(): Table
    {
        $params = [
            'name' => $this->config->getTableName()
        ];

        return Table::firstOrCreate($params, $params);
    }

    private function createTableFields(Table $table): void
    {
        foreach ($this->config->getTableFields() as $index => $fieldData) {
            $fieldData['order'] = $index + 1;

            $table->fields()->create($fieldData);
        }
    }
}
