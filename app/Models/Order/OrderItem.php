<?php

namespace App\Models\Order;

use App\Service\OrderItemService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id'
    ];

    public function data()
    {
        return $this->hasMany(OrderItemData::class, 'order_item_id');
    }

    public function getNameField(): string {
        $fieldId = OrderItemService::getItemNameField()->id;

        return OrderItemData::where('field_id', $fieldId)->where('order_item_id', $this->id)->first()->value;
    }
}
