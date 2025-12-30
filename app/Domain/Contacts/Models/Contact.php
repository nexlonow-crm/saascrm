<?php

namespace App\Domain\Contacts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToWorkspace;

class Contact extends Model
{
    use SoftDeletes, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id','owner_id','company_id',
        'first_name','last_name','email','phone','mobile','job_title',
        'street','city','state','postal_code','country',
        'lifecycle_stage','lead_source','status','extra'
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'primary_contact_id');
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
