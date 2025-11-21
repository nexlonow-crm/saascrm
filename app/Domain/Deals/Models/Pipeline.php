<?php

namespace App\Domain\Deals\Models;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'tenant_id',
        'name',
        'type',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function stages()
    {
        return $this->hasMany(Stage::class)->orderBy('position');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}
