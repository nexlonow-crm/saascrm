<?php

namespace App\Domain\Deals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToWorkspace;

class Deal extends Model
{
    use SoftDeletes, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id','owner_id','pipeline_id','stage_id',
        'company_id','primary_contact_id',
        'title','amount','currency','status',
        'expected_close_date','closed_at','extra'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_close_date' => 'date',
        'closed_at' => 'datetime',
        'extra' => 'array',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
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

    public function notes()
    {
        return $this->morphMany(Note::class, 'subject');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
