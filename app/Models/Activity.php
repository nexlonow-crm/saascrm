<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ← correct

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id', 'tenant_id',
        'subject_id', 'subject_type',
        'owner_id',
        'type', 'title', 'notes',
        'due_date', 'is_completed'
    ];

    // polymorphic relationship
    public function subject()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
