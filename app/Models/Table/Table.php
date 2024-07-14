<?php

namespace App\Models\Table;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function fields()
    {
        return $this->hasMany(TableField::class)->orderBy('order');
    }
}
