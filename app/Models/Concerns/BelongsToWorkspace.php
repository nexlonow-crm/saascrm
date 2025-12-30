<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToWorkspace
{
    protected static function bootBelongsToWorkspace(): void
    {
        static::addGlobalScope('workspace', function (Builder $builder) {
            if (app()->bound('currentWorkspace') && app('currentWorkspace')) {
                $wsId = app('currentWorkspace')->id;
                $builder->where($builder->getModel()->getTable() . '.workspace_id', $wsId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->workspace_id) && app()->bound('currentWorkspace') && app('currentWorkspace')) {
                $model->workspace_id = app('currentWorkspace')->id;
            }
        });
    }
}
