<?php

namespace App\Http\Middleware;
use App\Models\Workspace;
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
        $workspace = $request->route('workspace');

        // (keep your fallback if you added it)
        if (is_string($workspace)) {
            $workspace = \App\Models\Workspace::where('slug', $workspace)->firstOrFail();
        }

        abort_if($workspace->status !== 'active', 403);

        $user = $request->user();
        $isMember = $workspace->users()->where('users.id', $user->id)->exists();
        abort_unless($isMember, 403);

        app()->instance('currentWorkspace', $workspace);

        // ✅ Save last active workspace (avoid extra writes)
        if ($user && $user->last_workspace_id !== $workspace->id) {
            $user->forceFill(['last_workspace_id' => $workspace->id])->save();
        }

        return $next($request);
    }


}
