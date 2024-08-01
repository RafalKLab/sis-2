<?php

namespace App\Models\Order;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemData extends Model
{
    use HasFactory;

    protected $table = 'order_items_data';

    protected $fillable = [
        'value',
        'field_id',
        'last_updated_by_user_id'
    ];

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by_user_id');
    }
}
