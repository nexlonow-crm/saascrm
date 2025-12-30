<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'plan', 'is_active', 'trial_ends_at',
        // optional billing fields:
        'billing_status', 'stripe_customer_id', 'stripe_subscription_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'account_users')
            ->withPivot(['role'])
            ->withTimestamps();
    }
}
