<?php

namespace App\Http\Controllers;

use App\Domain\Deals\Models\Pipeline;
use Illuminate\Http\Request;

class PipelinesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $pipelines = Pipeline::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->withCount('stages')
            ->orderBy('name')
            ->paginate(15);

        return view('pipelines.index', compact('pipelines'));
    }

    public function create()
    {
        return view('pipelines.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'type'       => ['nullable', 'string', 'max:50'], // sales, job_search, etc.
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['account_id'] = $user->account_id;
        $data['tenant_id']  = $user->tenant_id;
        $data['is_default'] = $request->boolean('is_default');

        // if this is set as default, unset other defaults for this tenant
        if ($data['is_default']) {
            Pipeline::where('account_id', $user->account_id)
                ->where('tenant_id', $user->tenant_id)
                ->update(['is_default' => false]);
        }

        $pipeline = Pipeline::create($data);

        return redirect()
            ->route('pipelines.edit', $pipeline)
            ->with('status', 'Pipeline created. You can now add stages.');
    }

    public function edit(Pipeline $pipeline)
    {
        $this->authorizePipeline($pipeline);

        $pipeline->load(['stages' => function ($q) {
            $q->orderBy('position');
        }]);

        return view('pipelines.edit', compact('pipeline'));
    }

    public function update(Request $request, Pipeline $pipeline)
    {
        $this->authorizePipeline($pipeline);

        $user = $request->user();

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'type'       => ['nullable', 'string', 'max:50'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Pipeline::where('account_id', $user->account_id)
                ->where('tenant_id', $user->tenant_id)
                ->update(['is_default' => false]);
        }

        $pipeline->update($data);

        return redirect()
            ->route('pipelines.index')
            ->with('status', 'Pipeline updated.');
    }

    public function destroy(Pipeline $pipeline)
    {
        $this->authorizePipeline($pipeline);

        // You may want to prevent delete if deals exist in this pipeline
        if ($pipeline->deals()->exists()) {
            return back()->withErrors('Cannot delete pipeline that has deals.');
        }

        $pipeline->delete();

        return redirect()
            ->route('pipelines.index')
            ->with('status', 'Pipeline deleted.');
    }

    protected function authorizePipeline(Pipeline $pipeline): void
    {
        $user = auth()->user();

        if ($pipeline->account_id !== $user->account_id || $pipeline->tenant_id !== $user->tenant_id) {
            abort(403);
        }
    }
}
