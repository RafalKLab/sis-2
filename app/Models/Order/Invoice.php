<?php

namespace App\Models\Order;

use App\Models\Table\TableField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'issue_date',
        'pay_until_date',
        'status',
        'order_id',
        'field_id',
        'customer',
        'is_trans',
        'sum',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function field()
    {
        return $this->belongsTo(TableField::class, 'field_id');
    }
}
