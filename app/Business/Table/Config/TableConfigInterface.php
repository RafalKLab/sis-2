<?php

namespace App\Business\Table\Config;

interface TableConfigInterface
{
    public function getTableName(): string;

    public function getTableFields(): array;
}
