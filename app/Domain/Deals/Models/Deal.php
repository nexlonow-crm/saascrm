<?php

namespace App\Domain\Deals\Models;

use App\Domain\Companies\Models\Company;
use App\Domain\Contacts\Models\Contact;
use App\Domain\Activities\Models\Activity;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_OPEN = 'open';
    public const STATUS_WON  = 'won';
    public const STATUS_LOST = 'lost';

    protected $fillable = [
        'account_id',
        'tenant_id',
        'owner_id',
        'pipeline_id',
        'stage_id',
        'company_id',
        'primary_contact_id',
        'title',
        'amount',
        'currency',
        'status',
        'expected_close_date',
        'closed_at',
        'extra',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_close_date' => 'date',
        'closed_at' => 'datetime',
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

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function primaryContact()
    {
        return $this->belongsTo(Contact::class, 'primary_contact_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
