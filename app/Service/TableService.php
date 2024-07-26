<?php

namespace App\Service;

use App\Models\Table\TableField;

class TableService
{
    public static function getFieldByType(string $type): TableField
    {
       $field = TableField::where('type', $type)->first();

       if (!$field) {
           throw new \Exception(sprintf('Missing field with type: %s', $type));
       }

       return $field;
    }
}
