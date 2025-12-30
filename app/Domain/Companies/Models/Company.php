<?php

namespace App\Domain\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToWorkspace;

class Company extends Model
{
    use SoftDeletes, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id','owner_id','name','domain','website','phone','industry','size',
        'street','city','state','postal_code','country','extra'
    ];

    protected $casts = [
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

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
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
