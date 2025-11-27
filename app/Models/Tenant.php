<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['account_id', 'name', 'status'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /* -------------------------
     *  Plan / Feature helpers
     * ---------------------- */

    public function plan(): string
    {
        $plan = $this->account->plan ?? null;

        if (!$plan) {
            return config('features.default_plan', 'free');
        }

        return $plan;
    }

    public function features(): array
    {
        $plansConfig = config('features.plans', []);
        return $plansConfig[$this->plan()] ?? [];
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features(), true);
    }

    public function hasAnyFeature(array $features): bool
    {
        $enabled = $this->features();

        foreach ($features as $feature) {
            if (in_array($feature, $enabled, true)) {
                return true;
            }
        }

        return false;
    }
}
