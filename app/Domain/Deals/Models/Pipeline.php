<?php

namespace App\Domain\Deals\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToWorkspace;

class Pipeline extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id','name','is_default','type'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function stages()
    {
        return $this->hasMany(Stage::class)->orderBy('position');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}
