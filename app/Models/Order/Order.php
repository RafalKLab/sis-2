<?php

namespace App\Models\Order;

use App\Service\OrderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
    ];

    /**
     * Get the parent order if any
     */
    public function parent()
    {
        return $this->belongsTo(Order::class, 'parent_id');
    }

    /**
     * Get the child orders
     */
    public function children()
    {
        return $this->hasMany(Order::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'order_id');
    }

    public function data()
    {
        return $this->hasMany(OrderData::class, 'order_id');
    }

    public function getKeyField(): string
    {
        return OrderService::getKeyFieldFrom($this->id)->value;
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();


        static::created(function ($model) {

            if ($model->parent) {
                $keyFieldData = [
                    'value' => OrderService::generateKeyFieldForChild($model->id),
                    'field_id' => OrderService::getKeyField()->id,
                ];
            } else {
                $keyFieldData = [
                    'value' => OrderService::generateKeyField($model->id),
                    'field_id' => OrderService::getKeyField()->id,
                ];
            }

            $model->data()->create($keyFieldData);
        });
    }
}
