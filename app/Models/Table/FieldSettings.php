<?php

namespace App\Models\Table;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'order_id',
        'setting',
        'value'
    ];
}
