<?php

namespace App\Service;

use App\Models\Table\TableField;

class OrderService
{
    public static function getKeyField(): ?TableField
    {
        return TableField::where('type', 'id')->first();
    }
}
