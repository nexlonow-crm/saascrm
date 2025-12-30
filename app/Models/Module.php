<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['key','name','is_core'];

    protected $casts = [
        'is_core' => 'boolean',
    ];

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_modules')
            ->withPivot(['is_enabled','disabled_reason'])
            ->withTimestamps();
    }
}
