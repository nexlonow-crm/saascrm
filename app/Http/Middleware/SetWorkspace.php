<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetWorkspace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        /** @var \App\Models\Workspace $workspace */
        $workspace = $request->route('workspace');

        abort_if(!$workspace, 404);
        abort_if($workspace->status !== 'active', 403);

        $user = $request->user();

        $isMember = $workspace->users()->where('users.id', $user->id)->exists();
        abort_unless($isMember, 403);

        app()->instance('currentWorkspace', $workspace);
        $request->attributes->set('currentWorkspace', $workspace);

        return $next($request);
    }

}
