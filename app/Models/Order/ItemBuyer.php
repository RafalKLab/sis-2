<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBuyer extends Model
{
    protected $fillable = [
        'order_item_id',
        'name',
        'quantity',
        'address',
        'carrier',
        'trans_number',
        'last_country',
        'dep_country',
    ];

    use HasFactory;

    public function item()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
