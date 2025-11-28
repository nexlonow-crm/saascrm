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
            'label'       => ['nullable', 'string', 'max:255'],
            'badge_color' => ['nullable', 'string', 'max:20'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $maxPosition = $pipeline->stages()->max('position') ?? 0;

        $pipeline->stages()->create([
            'name'        => $data['name'],
            'label'       => $data['label'] ?? null,
            'badge_color' => $data['badge_color'] ?? null,
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
            'label'       => ['nullable', 'string', 'max:255'],
            'badge_color' => ['nullable', 'string', 'max:20'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'position'    => ['nullable', 'integer', 'min:0'],
        ]);

        if (!array_key_exists('position', $data)) {
            $data['position'] = $stage->position;
        }

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

        $order = $request->input('order'); // e.g. "5,3,2,7"
        if (!$order) {
            return back()->with('status', 'Nothing to reorder.');
        }

        $ids = array_filter(explode(',', $order));

        foreach ($ids as $index => $id) {
            $stage = $pipeline->stages()->where('id', $id)->first();
            if ($stage) {
                $stage->position = $index + 1; // 1-based ordering
                $stage->save();
            }
        }

        return back()->with('status', 'Stage order updated.');
    }

}
