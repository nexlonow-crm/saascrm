<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureWorkspaceFeature
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        // workspace is set by SetWorkspace middleware
        $workspace = app()->bound('currentWorkspace') ? app('currentWorkspace') : null;

        abort_unless($workspace, 404);
        abort_unless($workspace->hasFeature($feature), 403);

        // if (!$workspace->hasFeature($feature)) {
        //     return redirect()
        //         ->route('billing.upgrade') // create later
        //         ->with('error', 'This feature is not available on your plan.');
        // }


        return $next($request);
    }
}
