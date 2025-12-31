<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'name',
        'slug',
        'status',
        'industry_key',
        'timezone',
        'currency',
        'owner_user_id',
    ];

    // Use slug in routes
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_users')
            ->withPivot('role')
            ->withTimestamps();
    }
}
