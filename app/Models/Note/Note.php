<?php

namespace App\Models\Note;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'author',
        'message',
        'identifier',
        'target',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'author');
    }
}
