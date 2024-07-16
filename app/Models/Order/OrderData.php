<?php

namespace App\Models\Order;

use App\Models\Table\TableField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderData extends Model
{
    use HasFactory;

    protected $table = 'order_data';

    protected $fillable = ['value', 'field_id'];

    public function field()
    {
        return $this->belongsTo(TableField::class);
    }
}
