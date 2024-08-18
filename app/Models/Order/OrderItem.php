<?php

namespace App\Models\Order;

use App\Models\Table\Table;
use App\Models\Table\TableField;
use App\Service\OrderItemService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use shared\ConfigDefaultInterface;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function data()
    {
        return $this->hasMany(OrderItemData::class, 'order_item_id');
    }

    public function getNameField(): string
    {
        $fieldId = OrderItemService::getItemNameField()->id;

        return OrderItemData::where('field_id', $fieldId)->where('order_item_id', $this->id)->first()->value;
    }

    public function buyers()
    {
        return $this->hasMany(ItemBuyer::class, 'order_item_id');
    }

    public function getPurchaseSum(): string
    {
        $purchaseSumFieldId = TableField::where('type', ConfigDefaultInterface::FIELD_TYPE_PURCHASE_SUM)->first()->id;

        $sum = OrderItemData::where('field_id', $purchaseSumFieldId)->where('order_item_id', $this->id)->first()?->value;
        $sum = number_format($sum, 2, '.', '');

        return $sum;
    }

    public function getSalesNumber(): string
    {
        $salesNumberFieldId = TableField::where('type', ConfigDefaultInterface::FIELD_TYPE_SALES_NUMBER)->first()->id;

        $salesNumber = OrderItemData::where('field_id', $salesNumberFieldId)->where('order_item_id', $this->id)->first()?->value;
        $salesNumber = number_format($salesNumber, 2, '.', '');

        return $salesNumber;
    }

    public function getSalesSum(): string
    {
        $purchaseSumFieldId = TableField::where('type', ConfigDefaultInterface::FIELD_TYPE_SALES_SUM)->first()->id;

        $sum = OrderItemData::where('field_id', $purchaseSumFieldId)->where('order_item_id', $this->id)->first()?->value;
        $sum = number_format($sum, 2, '.', '');

        return $sum;
    }

    public function getItemOtherCosts(): string
    {
        $itemOtherCosts = TableField::where('type', ConfigDefaultInterface::FIELD_TYPE_ITEM_OTHER_COSTS)->first()->id;

        $costs = OrderItemData::where('field_id', $itemOtherCosts)->where('order_item_id', $this->id)->first()?->value;
        $costs = number_format($costs, 2, '.', '');

        return $costs;
    }
}
