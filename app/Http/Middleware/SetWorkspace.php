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

        // ✅ If binding failed and we got slug string, resolve manually
        if (is_string($workspace)) {
            $workspace = Workspace::where('slug', $workspace)->firstOrFail();
        }

        abort_if($workspace->status !== 'active', 403);

        $user = $request->user();
        $isMember = $workspace->users()->where('users.id', $user->id)->exists();
        abort_unless($isMember, 403);

        app()->instance('currentWorkspace', $workspace);

        return $next($request);
    }

}
