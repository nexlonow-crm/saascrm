<?php

namespace App\Http\Controllers;

use App\Domain\Deals\Models\Pipeline;
use Illuminate\Http\Request;

class PipelinesController extends Controller
{
    private function ws()
    {
        return app('currentWorkspace'); // set by SetWorkspace middleware
    }

    private function findPipelineOrFail($pipelineId): Pipeline
    {
        $ws = $this->ws();

        return Pipeline::withoutGlobalScopes()
            ->where('workspace_id', $ws->id)
            ->whereKey($pipelineId)
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $ws = $this->ws();

        $pipelines = Pipeline::query()
            ->where('workspace_id', $ws->id)
            ->withCount('stages')
            ->orderBy('name')
            ->paginate(15);

        return view('pipelines.index', compact('pipelines', 'ws'));
    }

    public function create()
    {
        $ws = $this->ws();
        return view('pipelines.create', compact('ws'));
    }

    public function store(Request $request)
    {
        $ws = $this->ws();

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'type'       => ['nullable', 'string', 'max:50'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['workspace_id'] = $ws->id;
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Pipeline::where('workspace_id', $ws->id)->update(['is_default' => false]);
        }

        $pipeline = Pipeline::create($data);

        return redirect()
            ->route('pipelines.edit', ['workspace' => $ws->slug, 'pipeline' => $pipeline->id])
            ->with('status', 'Pipeline created. You can now add stages.');
    }

    public function edit($workspace, $pipeline)
    {
        $ws = $this->ws();

        $pipeline = Pipeline::where('workspace_id', $ws->id)
            ->where('id', $pipeline)
            ->firstOrFail();

        $pipeline->load(['stages' => function ($q) {
            $q->orderBy('position');
        }]);

        return view('pipelines.edit', compact('pipeline', 'ws'));
    }


    public function update(Request $request, $workspace, $pipeline)
    {
        $ws = $this->ws();

        $pipeline = Pipeline::where('workspace_id', $ws->id)
            ->where('id', $pipeline)
            ->firstOrFail();

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'type'       => ['nullable', 'string', 'max:50'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Pipeline::where('workspace_id', $ws->id)->update(['is_default' => false]);
        }

        $pipeline->update($data);

        return redirect()
            ->route('pipelines.index', ['workspace' => $ws->slug])
            ->with('status', 'Pipeline updated.');
    }


    public function destroy($workspace, $pipeline)
    {
        $ws = $this->ws();

        $pipeline = Pipeline::where('workspace_id', $ws->id)
            ->where('id', $pipeline)
            ->firstOrFail();

        if ($pipeline->deals()->exists()) {
            return back()->withErrors('Cannot delete pipeline that has deals.');
        }

        $pipeline->delete();

        return redirect()
            ->route('pipelines.index', ['workspace' => $ws->slug])
            ->with('status', 'Pipeline deleted.');
    }

}
