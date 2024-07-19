<?php

namespace App\Models\Order;

use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Business\BusinessFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class File extends Model
{
    public string $cacheOrderKeyField;

    protected $fillable = [
        'order_id',
        'user_id',
        'file_name',
        'file_path'
    ];

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $orderKey = $model->order->getKeyField();

            $factory = new BusinessFactory();
            $author = Auth::user();
            $authorEmail = $author ? $author->email : 'System';

            $transfer = $factory
                ->getActivityLogTransferObject()
                ->setUser($authorEmail)
                ->setTitle(ActivityLogConstants::INFO_LOG)
                ->setAction(ActivityLogConstants::ACTION_UPLOAD)
                ->setNewData(sprintf(sprintf('file %s for order %s', $model->file_name, $orderKey)));

            $factory->createActivityLogManager()->log($transfer);
        });

        static::deleting(function ($model) {
            // Cache the necessary data before the actual deletion happens
            $model->cacheOrderKeyField = $model->order->getKeyField();
        });

        static::deleted(function ($model) {
            $orderKey = $model->cacheOrderKeyField ?? 'unknown';

            $factory = new BusinessFactory();
            $author = Auth::user();
            $authorEmail = $author ? $author->email : 'System';

            $transfer = $factory
                ->getActivityLogTransferObject()
                ->setUser($authorEmail)
                ->setTitle(ActivityLogConstants::DANGER_LOG)
                ->setAction(ActivityLogConstants::ACTION_DELETE)
                ->setNewData(sprintf('file %s of order %s', $model->file_name, $orderKey));

            $factory->createActivityLogManager()->log($transfer);
        });
    }
}
