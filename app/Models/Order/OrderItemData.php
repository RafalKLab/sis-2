<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemData extends Model
{
    use HasFactory;

    protected $table = 'order_items_data';

    protected $fillable = [
        'value',
        'field_id'
    ];
}
