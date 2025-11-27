<?php

namespace App\Http\Controllers;

use App\Domain\Deals\Models\Pipeline;
use App\Domain\Deals\Models\Stage;
use Illuminate\Http\Request;

class PipelineStageController extends Controller
{
    protected function authorizePipeline(Pipeline $pipeline): void
    {
        $user = auth()->user();

        if ($pipeline->account_id !== $user->account_id || $pipeline->tenant_id !== $user->tenant_id) {
            abort(403);
        }
    }

    public function store(Request $request, Pipeline $pipeline)
    {
        $this->authorizePipeline($pipeline);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $maxPosition = $pipeline->stages()->max('position') ?? 0;

        $pipeline->stages()->create([
            'name'        => $data['name'],
            'probability' => $data['probability'] ?? null,
            'position'    => $maxPosition + 1,
        ]);

        return back()->with('status', 'Stage added.');
    }

    public function update(Request $request, Pipeline $pipeline, Stage $stage)
    {
        $this->authorizePipeline($pipeline);

        if ($stage->pipeline_id !== $pipeline->id) {
            abort(403);
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $stage->update($data);

        return back()->with('status', 'Stage updated.');
    }

    public function destroy(Pipeline $pipeline, Stage $stage)
    {
        $this->authorizePipeline($pipeline);

        if ($stage->pipeline_id !== $pipeline->id) {
            abort(403);
        }

        // Optionally prevent delete if deals exist in this stage
        if ($stage->deals()->exists()) {
            return back()->withErrors('Cannot delete stage that has deals.');
        }

        $stage->delete();

        return back()->with('status', 'Stage deleted.');
    }

    public function reorder(Request $request, Pipeline $pipeline)
    {
        $this->authorizePipeline($pipeline);

        $positions = $request->input('positions', []); // [stage_id => position]

        foreach ($positions as $stageId => $position) {
            $stage = $pipeline->stages()->where('id', $stageId)->first();
            if ($stage) {
                $stage->position = (int) $position;
                $stage->save();
            }
        }

        return back()->with('status', 'Stage order updated.');
    }
}
