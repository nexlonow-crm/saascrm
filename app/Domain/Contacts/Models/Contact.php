<?php

namespace App\Domain\Contacts\Models;


use App\Domain\Companies\Models\Company;
use App\Domain\Deals\Models\Deal;
use App\Domain\Activities\Models\Activity;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'tenant_id',
        'owner_id',
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'job_title',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
        'lifecycle_stage',
        'lead_source',
        'status',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    /** -----------------------
     *  Relationships
     * ------------------------ */

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'primary_contact_id');
    }

    /** -----------------------
     *  Accessors / Helpers
     * ------------------------ */

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /** -----------------------
     *  Scopes
     * ------------------------ */

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
