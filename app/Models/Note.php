<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'tenant_id',
        'subject_id',
        'subject_type',
        'user_id',
        'body',
        'is_pinned',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
