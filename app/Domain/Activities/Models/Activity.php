<?php

namespace App\Domain\Activities\Models;

use App\Domain\Contacts\Models\Contact;
use App\Domain\Companies\Models\Company;
use App\Domain\Deals\Models\Deal;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'tenant_id',
        'user_id',
        'contact_id',
        'company_id',
        'deal_id',
        'subject',
        'type',
        'body',
        'due_at',
        'done_at',
        'extra',
    ];

    

    protected $casts = [
        'due_at' => 'datetime',
        'done_at' => 'datetime',
        'extra' => 'array',
        'due_date' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
