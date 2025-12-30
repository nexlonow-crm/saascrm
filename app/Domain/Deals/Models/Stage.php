<?php

namespace App\Domain\Deals\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToWorkspace;

class Stage extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id','pipeline_id','name','probability','position'];

    protected $casts = [
        'probability' => 'integer',
        'position' => 'integer',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}
