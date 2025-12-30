<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workspace extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id','name','slug','status','industry_key','timezone','currency','owner_user_id'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_users')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function pipelines()
    {
        return $this->hasMany(Pipeline::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'workspace_modules')
            ->withPivot(['is_enabled', 'disabled_reason'])
            ->withTimestamps();
    }
}
