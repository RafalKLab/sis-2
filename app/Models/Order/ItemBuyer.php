<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBuyer extends Model
{
    protected $fillable = [
        'order_item_id',
        'name',
        'quantity'
    ];

    use HasFactory;
}
