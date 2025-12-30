<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Workspace;

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

        $slug = Str::slug($request->name);

        // Ensure unique slug (simple approach)
        $original = $slug;
        $i = 1;
        while (Workspace::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        $workspace = Workspace::create([
            'name' => $request->name,
            'slug' => $slug,
            'owner_id' => auth()->id(),
        ]);

        // Attach user to workspace depending on your schema
        // If you have pivot:
        // $workspace->users()->attach(auth()->id(), ['role' => 'owner']);

        return redirect()->route('dashboard', ['workspace' => $workspace->slug]);
    }
}
