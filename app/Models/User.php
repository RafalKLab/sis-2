<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Business\ActivityLog\Config\ActivityLogConstants;
use App\Business\BusinessFactory;
use App\Business\Table\Config\TableConfig;
use App\Models\Order\Order;
use App\Models\Table\Table;
use App\Models\Table\TableField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use shared\ConfigDefaultInterface;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    public const DO_NOT_LOG = [
        'is_blocked',
        'remember_token',
        'password',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_blocked',
        'is_root'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function fields()
    {
        return $this->belongsToMany(TableField::class, 'field_user', 'user_id', 'field_id')
            ->orderBy('order');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getAssignedFields()
    {
        if ($this->hasRole(ConfigDefaultInterface::ROLE_ADMIN)) {
            return Table::where('name', TableConfig::MAIN_TABLE_NAME)->first()->fields;
        } else {
            return $this->fields;
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $factory = new BusinessFactory();
            $author = Auth::user(); // Retrieve the authenticated user
            $authorEmail = $author ? $author->email : 'Anonymous'; // Handle cases where there might not be an authenticated user

            // Get the changed attributes
            $changes = $model->getDirty();

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
                    ->setOldData(sprintf('User %s (id:%s) attribute %s: %s ',$model->email, $model->id, $attribute, $oldValue))
                    ->setNewData($newValue);

               $factory->createActivityLogManager()->log($transfer);
            }
        });

        static::created(function ($model) {
            $factory = new BusinessFactory();
            $author = Auth::user();
            $authorEmail = $author ? $author->email : 'System';

            $transfer = $factory
                ->getActivityLogTransferObject()
                ->setUser($authorEmail)
                ->setTitle(ActivityLogConstants::INFO_LOG)
                ->setAction(ActivityLogConstants::ACTION_ADD)
                ->setNewData(sprintf('User %s (id:%s)', $model->email, $model->id));

            $factory->createActivityLogManager()->log($transfer);
        });
    }
}
