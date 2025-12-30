<?php 
namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToWorkspace
{
    protected static function bootBelongsToWorkspace()
    {
        static::addGlobalScope('workspace', function (Builder $builder) {
            $workspace = app()->bound('currentWorkspace') ? app('currentWorkspace') : null;
            if ($workspace) {
                $builder->where($builder->getModel()->getTable().'.workspace_id', $workspace->id);
            }
        });

        static::creating(function ($model) {
            $workspace = app()->bound('currentWorkspace') ? app('currentWorkspace') : null;
            if ($workspace && empty($model->workspace_id)) {
                $model->workspace_id = $workspace->id;
            }
        });
    }
}
