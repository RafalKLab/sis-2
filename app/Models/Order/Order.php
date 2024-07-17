<?php

namespace App\Models\Order;

use App\Service\OrderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    use HasFactory;

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

    public function data()
    {
        return $this->hasMany(OrderData::class, 'order_id');
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



            //TODO: Implement logging
//            $orderKey = OrderService::getKeyFieldFrom($model->order_id)->value;
//
//            $factory = new BusinessFactory();
//            $author = Auth::user();
//            $authorEmail = $author ? $author->email : 'System';
//
//            $transfer = $factory
//                ->getActivityLogTransferObject()
//                ->setUser($authorEmail)
//                ->setTitle(ActivityLogConstants::INFO_LOG)
//                ->setAction(ActivityLogConstants::ACTION_ADD)
//                ->setNewData(sprintf(sprintf('order %s field %s: %s', $orderKey, $model->field->name, $model->value)));
//
//            $factory->createActivityLogManager()->log($transfer);
        });
    }
}
