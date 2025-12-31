<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    public function create()
    {
        return view('workspaces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = auth()->user();

        // 🔹 Get or create account (depends on your setup)
        $accountId = auth()->user()->account_id; // ✅ now always exists after register

        if (!$accountId) {
            abort(400, 'No account found for user');
        }

        // 🔹 Generate unique slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $i = 1;

        while (Workspace::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        // 🔹 Create workspace
        $workspace = Workspace::create([
            'account_id' => $accountId,
            'name' => $request->name,
            'slug' => $slug,
            'owner_user_id' => $user->id,
            'status' => 'active',
        ]);

        // 🔹 Attach user as owner
        $workspace->users()->attach($user->id, [
            'role' => 'owner',
        ]);


        // 🔹 Redirect to workspace dashboard
        return redirect()->route('dashboard', [
            'workspace' => $workspace->slug,
        ]);
    }
}
