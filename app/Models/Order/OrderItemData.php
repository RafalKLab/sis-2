<?php

namespace App\Models\Order;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Business\BusinessFactory;
use App\Models\Table\TableField;
use App\Models\User;
use App\Service\OrderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderItemData extends Model
{
    use HasFactory;

    protected $table = 'order_items_data';

    protected $fillable = [
        'value',
        'field_id',
        'last_updated_by_user_id'
    ];

    public const DO_NOT_LOG = [
        'last_updated_by_user_id',
        'field_id',
    ];

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by_user_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function field()
    {
        return $this->belongsTo(TableField::class, 'field_id');
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $factory = new BusinessFactory();
            $author = Auth::user();
            $authorEmail = $author ? $author->email : 'System';

            // Get the changed attributes
            $changes = $model->getDirty();

            $orderKey = OrderService::getKeyFieldFrom($model->orderItem->order_id)->value;

            // Log the old and new values
            foreach ($changes as $attribute => $newValue) {
                if (in_array($attribute, User::DO_NOT_LOG)) {
                    continue;
                }

                $oldValue = $model->getOriginal($attribute);

                $transfer = $factory
                    ->getActivityLogTransferObject()
                    ->setUser($authorEmail)
                    ->setTitle(ActivityLogConstants::INFO_LOG)
                    ->setAction(ActivityLogConstants::ACTION_UPDATE)
                    ->setOldData(sprintf('Order %s item %s field %s: %s', $orderKey, $model->orderItem->getNameField(), $model->field->name, $oldValue))
                    ->setNewData($newValue);

                $factory->createActivityLogManager()->log($transfer);
            }
        });

    }
}
