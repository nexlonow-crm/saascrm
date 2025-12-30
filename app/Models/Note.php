<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToWorkspace;

class Note extends Model
{
    use SoftDeletes, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id','user_id','subject_type','subject_id','body','is_pinned'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
