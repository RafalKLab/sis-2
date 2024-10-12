<?php

namespace App\Models\Order;

use App\Models\Company\Company;
use App\Models\User;
use App\Service\OrderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'user_id',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

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

    public function comments()
    {
        return $this->hasMany(Comment::class, 'order_id')->orderBy('created_at', 'desc');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function data()
    {
        return $this->hasMany(OrderData::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
