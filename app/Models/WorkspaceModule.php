<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkspaceModule extends Pivot
{
    protected $table = 'workspace_modules';

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected $fillable = ['workspace_id','module_id','is_enabled','disabled_reason'];
}
