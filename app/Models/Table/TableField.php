<?php

namespace App\Models\Table;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'order',
        'table_id',
        'color'
    ];
}
