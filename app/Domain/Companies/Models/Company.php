<?php

namespace App\Domain\Companies\Models;

use App\Domain\Contacts\Models\Contact;
use App\Domain\Deals\Models\Deal;
use App\Models\Tenant;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'tenant_id',
        'owner_id',
        'name',
        'domain',
        'website',
        'phone',
        'industry',
        'size',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
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

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    /** -----------------------
     *  Scopes
     * ------------------------ */

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->orderBy('due_date');
    }

}
