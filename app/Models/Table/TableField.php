<?php

namespace App\Models\Table;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Business\BusinessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TableField extends Model
{
    use HasFactory;

    public const DO_NOT_LOG = [
        'order',
    ];

    protected $fillable = [
        'name',
        'type',
        'order',
        'table_id',
        'color'
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $factory = new BusinessFactory();
            $author = Auth::user();
            $authorEmail = $author ? $author->email : 'System';

            $transfer = $factory
                ->getActivityLogTransferObject()
                ->setUser($authorEmail)
                ->setTitle(ActivityLogConstants::WARNING_LOG)
                ->setAction(ActivityLogConstants::ACTION_ADD)
                ->setNewData(sprintf('new field %s (id:%s)', $model->name, $model->id));

            $factory->createActivityLogManager()->log($transfer);
        });

        static::updating(function ($model) {
            $factory = new BusinessFactory();
            $author = Auth::user();
            $authorEmail = $author ? $author->email : 'Anonymous';

            // Get the changed attributes
            $changes = $model->getDirty();

            // Log the old and new values
            foreach ($changes as $attribute => $newValue) {
                if (in_array($attribute, TableField::DO_NOT_LOG)) {
                    continue;
                }

                $oldValue = $model->getOriginal($attribute);

                $transfer = $factory
                    ->getActivityLogTransferObject()
                    ->setUser($authorEmail)
                    ->setTitle(ActivityLogConstants::INFO_LOG)
                    ->setAction(ActivityLogConstants::ACTION_UPDATE)
                    ->setOldData(sprintf('Field %s (id:%s) attribute %s: %s ',$model->name, $model->id, $attribute, $oldValue))
                    ->setNewData($newValue);

                $factory->createActivityLogManager()->log($transfer);
            }
        });
    }
}
