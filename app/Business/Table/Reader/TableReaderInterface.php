<?php

namespace App\Business\Table\Reader;

use App\Models\Table\Table;
use App\Models\Table\TableField;

interface TableReaderInterface
{
    public function readTableData(?string $search): array;

    public function readTableFields(?Table $mainTable = null): array;

    public function getTableField(int $id): ?TableField;

    public function findMainTable(): ?Table;
}
