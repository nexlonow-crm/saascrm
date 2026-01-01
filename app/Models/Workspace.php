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
        return $this->belongsTo(\App\Models\Account::class);
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
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function hasFeature(string $feature): bool
    {
        // ✅ if plan is null, fallback to default_plan
        $plan = $this->account?->plan ?: config('plans.default_plan');

        $planFeatures = config("plans.plans.$plan", []);

        return in_array($feature, $planFeatures, true);
    }

    public function hasAnyFeature(array $features): bool
    {
        foreach ($features as $f) {
            if ($this->hasFeature($f)) return true;
        }
        return false;
    }

}
