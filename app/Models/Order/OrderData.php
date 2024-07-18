<?php

namespace App\Models\Order;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Business\BusinessFactory;
use App\Models\Table\TableField;
use App\Service\OrderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderData extends Model
{
    use HasFactory;

    protected $table = 'order_data';

    protected $fillable = ['value', 'field_id'];

    public function field()
    {
        return $this->belongsTo(TableField::class);
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

            $orderKey = OrderService::getKeyFieldFrom($model->order_id)->value;

            // Log the old and new values
            foreach ($changes as $attribute => $newValue) {

                $oldValue = $model->getOriginal($attribute);

                $transfer = $factory
                    ->getActivityLogTransferObject()
                    ->setUser($authorEmail)
                    ->setTitle(ActivityLogConstants::INFO_LOG)
                    ->setAction(ActivityLogConstants::ACTION_UPDATE)
                    ->setOldData(sprintf('Order %s field %s: %s', $orderKey, $model->field->name, $oldValue))
                    ->setNewData($newValue);

                $factory->createActivityLogManager()->log($transfer);
            }
        });

        static::created(function ($model) {
            $orderKey = OrderService::getKeyFieldFrom($model->order_id)->value;

            $factory = new BusinessFactory();
            $author = Auth::user();
            $authorEmail = $author ? $author->email : 'System';

            $transfer = $factory
                ->getActivityLogTransferObject()
                ->setUser($authorEmail)
                ->setTitle(ActivityLogConstants::INFO_LOG)
                ->setAction(ActivityLogConstants::ACTION_ADD)
                ->setNewData(sprintf(sprintf('order %s field %s: %s', $orderKey, $model->field->name, $model->value)));

            $factory->createActivityLogManager()->log($transfer);
        });
    }
}
