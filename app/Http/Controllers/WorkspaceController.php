<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Domain\Deals\Models\Stage;
use Illuminate\Support\Facades\DB;
use App\Domain\Deals\Models\Pipeline;
use App\Services\WorkspaceProvisioner;



class WorkspaceController extends Controller
{
    public function create()
    {
        return view('workspaces.create');
    }

    public function store(Request $request, WorkspaceProvisioner $provisioner)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry_key' => ['required', 'string', 'in:'.implode(',', array_keys(config('workspace_templates.options')))],
        ]);

        $user = auth()->user();
        $accountId = $user->account_id;

        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $i = 2;

        while (Workspace::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $workspace = \DB::transaction(function () use ($user, $accountId, $request, $slug) {
            $workspace = Workspace::create([
                'account_id' => $accountId,
                'name' => $request->name,
                'slug' => $slug,
                'owner_user_id' => $user->id,
                'status' => 'active',
                'industry_key' => $request->industry_key, // ✅ save template choice
            ]);

            $workspace->users()->attach($user->id, ['role' => 'owner']);

            return $workspace;
        });

        $provisioner->seedWorkspace($workspace);

        return redirect()->route('dashboard', ['workspace' => $workspace->slug]);
    }


    public function index()
    {
        $user = auth()->user();
        $workspaces = $user->workspaces()->orderBy('name')->get();

        return view('workspaces.index', compact('workspaces'));
    }

    /**
     * Switch workspace (POST from dropdown)
     * Redirect to that workspace dashboard.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'workspace_id' => ['required', 'integer'],
        ]);

        $user = auth()->user();

        $workspace = $user->workspaces()
            ->where('workspaces.id', $request->workspace_id)
            ->firstOrFail();

        return redirect()->route('dashboard', ['workspace' => $workspace->slug]);
    }


}
