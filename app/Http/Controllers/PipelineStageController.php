<?php

namespace App\Http\Controllers;

use App\Domain\Deals\Models\Pipeline;
use App\Domain\Deals\Models\Stage;
use Illuminate\Http\Request;

class PipelineStageController extends Controller
{
    private function ws()
    {
        return app('currentWorkspace'); // set by SetWorkspace middleware
    }

    private function findPipelineOrFail($pipelineId): Pipeline
    {
        $ws = $this->ws();

        return Pipeline::where('workspace_id', $ws->id)
            ->where('id', $pipelineId)
            ->firstOrFail();
    }

    private function findStageOrFail(Pipeline $pipeline, $stageId): Stage
    {
        return Stage::where('workspace_id', $pipeline->workspace_id)
            ->where('pipeline_id', $pipeline->id)
            ->where('id', $stageId)
            ->firstOrFail();
    }

    // POST w/{workspace}/pipelines/{pipeline}/stages
    public function store(Request $request, $workspace, $pipeline)
    {
        $pipeline = $this->findPipelineOrFail($pipeline);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'label'       => ['nullable', 'string', 'max:255'],
            'badge_color' => ['nullable', 'string', 'max:20'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $maxPosition = $pipeline->stages()->max('position') ?? 0;

        $pipeline->stages()->create([
            'workspace_id' => $pipeline->workspace_id,
            'name'         => $data['name'],
            'label'        => $data['label'] ?? null,
            'badge_color'  => $data['badge_color'] ?? null,
            'probability'  => $data['probability'] ?? null,
            'position'     => $maxPosition + 1,
        ]);

        return back()->with('status', 'Stage added.');
    }

    // PUT w/{workspace}/pipelines/{pipeline}/stages/{stage}
    public function update(Request $request, $workspace, $pipeline, $stage)
    {
        $pipeline = $this->findPipelineOrFail($pipeline);
        $stage = $this->findStageOrFail($pipeline, $stage);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'label'       => ['nullable', 'string', 'max:255'],
            'badge_color' => ['nullable', 'string', 'max:20'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'position'    => ['nullable', 'integer', 'min:1'],
        ]);

        // Keep position unchanged if not sent
        if (!array_key_exists('position', $data)) {
            $data['position'] = $stage->position;
        }

        $stage->update($data);

        return back()->with('status', 'Stage updated.');
    }

    // DELETE w/{workspace}/pipelines/{pipeline}/stages/{stage}
    public function destroy($workspace, $pipeline, $stage)
    {
        $pipeline = $this->findPipelineOrFail($pipeline);
        $stage = $this->findStageOrFail($pipeline, $stage);

        // Prevent delete if deals exist in this stage
        if (method_exists($stage, 'deals') && $stage->deals()->exists()) {
            return back()->withErrors('Cannot delete stage that has deals.');
        }

        $stage->delete();

        return back()->with('status', 'Stage deleted.');
    }

    // POST w/{workspace}/pipelines/{pipeline}/stages/reorder
    public function reorder(Request $request, $workspace, $pipeline)
    {
        $pipeline = $this->findPipelineOrFail($pipeline);

        $order = $request->input('order'); // e.g. "5,3,2,7"
        if (!$order) {
            return back()->with('status', 'Nothing to reorder.');
        }

        $ids = array_values(array_filter(array_map('trim', explode(',', $order))));
        if (empty($ids)) {
            return back()->with('status', 'Nothing to reorder.');
        }

        // Only stages that belong to this pipeline + workspace
        $stages = Stage::where('workspace_id', $pipeline->workspace_id)
            ->where('pipeline_id', $pipeline->id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        foreach ($ids as $index => $id) {
            if (isset($stages[$id])) {
                $stages[$id]->update(['position' => $index + 1]); // 1-based
            }
        }

        return back()->with('status', 'Stage order updated.');
    }
}
