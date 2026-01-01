<?php

namespace App\Services;

use App\Domain\Deals\Models\Pipeline;
use App\Domain\Deals\Models\Stage;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class WorkspaceProvisioner
{
    public function seedDefaultPipeline(Workspace $workspace): void
    {
        DB::transaction(function () use ($workspace) {

            // Prevent duplicates (if called twice)
            $existing = Pipeline::where('workspace_id', $workspace->id)
                ->where('is_default', true)
                ->first();

            if ($existing) {
                return;
            }

            // Ensure only one default per workspace
            Pipeline::where('workspace_id', $workspace->id)->update(['is_default' => false]);

            $pipeline = Pipeline::create([
                'workspace_id' => $workspace->id,
                'name' => 'Sales Pipeline',
                'is_default' => true,
                'type' => 'sales',
            ]);

            $stages = [
                ['name' => 'Lead',        'probability' => 10,  'position' => 1],
                ['name' => 'Qualified',   'probability' => 25,  'position' => 2],
                ['name' => 'Proposal',    'probability' => 50,  'position' => 3],
                ['name' => 'Negotiation', 'probability' => 75,  'position' => 4],
                ['name' => 'Won',         'probability' => 100, 'position' => 5],
                ['name' => 'Lost',        'probability' => 0,   'position' => 6],
            ];

            foreach ($stages as $s) {
                Stage::create([
                    'workspace_id' => $workspace->id,
                    'pipeline_id' => $pipeline->id,
                    'name' => $s['name'],
                    'probability' => $s['probability'],
                    'position' => $s['position'],
                ]);
            }
        });
    }
    public function seedWorkspace(Workspace $workspace): void
    {
        $template = $workspace->industry_key ?? 'sales';
        $this->seedPipelineTemplate($workspace, $template);
    }

    private function seedPipelineTemplate(Workspace $workspace, string $template): void
    {
        DB::transaction(function () use ($workspace, $template) {

            // If a default pipeline already exists, don't seed again
            $exists = Pipeline::where('workspace_id', $workspace->id)
                ->where('is_default', true)
                ->exists();

            if ($exists) return;

            // Ensure only one default
            Pipeline::where('workspace_id', $workspace->id)->update(['is_default' => false]);

            // Pick template
            [$pipelineName, $pipelineType, $stages] = $this->templateData($template);

            $pipeline = Pipeline::create([
                'workspace_id' => $workspace->id,
                'name' => $pipelineName,
                'is_default' => true,
                'type' => $pipelineType,
            ]);

            foreach ($stages as $s) {
                Stage::create([
                    'workspace_id' => $workspace->id,
                    'pipeline_id' => $pipeline->id,
                    'name' => $s['name'],
                    'probability' => $s['probability'],
                    'position' => $s['position'],
                ]);
            }
        });
    }

    private function templateData(string $template): array
    {
        $templates = [];

        $templates['sales'] = [
            'Sales Pipeline',
            'sales',
            [
                ['name' => 'Lead',        'probability' => 10,  'position' => 1],
                ['name' => 'Qualified',   'probability' => 25,  'position' => 2],
                ['name' => 'Proposal',    'probability' => 50,  'position' => 3],
                ['name' => 'Negotiation', 'probability' => 75,  'position' => 4],
                ['name' => 'Won',         'probability' => 100, 'position' => 5],
                ['name' => 'Lost',        'probability' => 0,   'position' => 6],
            ],
        ];

        $templates['roofing'] = [
            'Roofing Jobs',
            'roofing',
            [
                ['name' => 'New Lead',           'probability' => 10,  'position' => 1],
                ['name' => 'Inspection Scheduled','probability' => 20, 'position' => 2],
                ['name' => 'Estimate Sent',      'probability' => 40,  'position' => 3],
                ['name' => 'Insurance / Approval','probability' => 60, 'position' => 4],
                ['name' => 'Scheduled',          'probability' => 75,  'position' => 5],
                ['name' => 'Completed',          'probability' => 100, 'position' => 6],
                ['name' => 'Lost',               'probability' => 0,   'position' => 7],
            ],
        ];

        $templates['job_search'] = [
            'Job Applications',
            'job_search',
            [
                ['name' => 'Saved',        'probability' => 5,   'position' => 1],
                ['name' => 'Applied',      'probability' => 15,  'position' => 2],
                ['name' => 'Interview',    'probability' => 50,  'position' => 3],
                ['name' => 'Offer',        'probability' => 80,  'position' => 4],
                ['name' => 'Accepted',     'probability' => 100, 'position' => 5],
                ['name' => 'Rejected',     'probability' => 0,   'position' => 6],
            ],
        ];

        $templates['personal'] = [
            'Personal Relationships',
            'personal',
            [
                ['name' => 'Acquaintance', 'probability' => 10,  'position' => 1],
                ['name' => 'Friend',       'probability' => 40,  'position' => 2],
                ['name' => 'Close Friend', 'probability' => 70,  'position' => 3],
                ['name' => 'Family',       'probability' => 100, 'position' => 4],
            ],
        ];

        $templates['auto_repair'] = [
            'Auto Repair Jobs',
            'auto_repair',
            [
                ['name' => 'New Request',     'probability' => 10,  'position' => 1],
                ['name' => 'Diagnosis',       'probability' => 25,  'position' => 2],
                ['name' => 'Quote Approved',  'probability' => 50,  'position' => 3],
                ['name' => 'In Progress',     'probability' => 70,  'position' => 4],
                ['name' => 'Ready for Pickup','probability' => 90,  'position' => 5],
                ['name' => 'Paid & Closed',   'probability' => 100, 'position' => 6],
                ['name' => 'Cancelled',       'probability' => 0,   'position' => 7],
            ],
        ];

        return $templates[$template] ?? $templates['sales'];
    }


}
