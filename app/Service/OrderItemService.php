<?php

namespace App\Service;

use App\Models\Table\TableField;

class OrderItemService
{
    public static function getItemNameField(): TableField
    {
        return TableField::where('name', 'Prekės pavadinimas')->first();
    }
}
