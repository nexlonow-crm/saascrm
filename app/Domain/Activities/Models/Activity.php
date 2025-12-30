<?php
namespace App\Domain\Activities\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToWorkspace;

class Activity extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id','owner_id',
        'subject_type','subject_id',
        'type','title','notes','due_date','is_completed'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
