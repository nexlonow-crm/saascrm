<?php

namespace App\Http\Controllers;

use App\Domain\Deals\Models\Deal;
use App\Domain\Deals\Models\Pipeline;
use App\Domain\Deals\Models\Stage;
use App\Domain\Companies\Models\Company;
use App\Domain\Contacts\Models\Contact;
use Illuminate\Http\Request;

use App\Notifications\DealCreatedNotification;
use App\Notifications\DealWonNotification;

class DealsController extends Controller
{
    private function ws()
    {
        return app('currentWorkspace'); // set by SetWorkspace middleware
    }

    public function index()
    {
        $ws = $this->ws();

        $deals = Deal::where('workspace_id', $ws->id)
            ->with(['company', 'primaryContact', 'stage', 'pipeline'])
            ->latest()
            ->paginate(15);

        return view('deals.index', compact('deals'));
    }

    public function create()
    {
        $ws = $this->ws();

        $pipelines = Pipeline::where('workspace_id', $ws->id)
            ->with(['stages' => fn($q) => $q->orderBy('position')])
            ->orderBy('name')
            ->get();

        $companies = Company::where('workspace_id', $ws->id)
            ->orderBy('name')
            ->get();

        $contacts = Contact::where('workspace_id', $ws->id)
            ->orderBy('first_name')
            ->get();

        return view('deals.create', compact('pipelines', 'companies', 'contacts'));
    }

    public function store(Request $request)
    {
        $ws = $this->ws();
        $user = $request->user();

        $data = $request->validate([
            'title'              => ['required', 'string', 'max:255'],
            'pipeline_id'        => ['required', 'integer'],
            'stage_id'           => ['required', 'integer'],
            'company_id'         => ['nullable', 'integer'],
            'primary_contact_id' => ['nullable', 'integer'],
            'amount'             => ['nullable', 'numeric'],
            'currency'           => ['nullable', 'string', 'size:3'],
            'status'             => ['nullable', 'string', 'max:20'],
            'expected_close_date'=> ['nullable', 'date'],
        ]);

        // ✅ Validate pipeline belongs to workspace
        $pipeline = Pipeline::where('workspace_id', $ws->id)
            ->where('id', $data['pipeline_id'])
            ->firstOrFail();

        // ✅ Validate stage belongs to the SAME pipeline (+ workspace)
        $stage = Stage::where('workspace_id', $ws->id)
            ->where('pipeline_id', $pipeline->id)
            ->where('id', $data['stage_id'])
            ->firstOrFail();

        // ✅ Optional relations must belong to same workspace
        if (!empty($data['company_id'])) {
            Company::where('workspace_id', $ws->id)->where('id', $data['company_id'])->firstOrFail();
        }

        if (!empty($data['primary_contact_id'])) {
            Contact::where('workspace_id', $ws->id)->where('id', $data['primary_contact_id'])->firstOrFail();
        }

        $data['workspace_id'] = $ws->id;
        $data['owner_id']     = $user->id;
        $data['currency']     = $data['currency'] ?? 'USD';
        $data['status']       = $data['status'] ?? Deal::STATUS_OPEN;

        $deal = Deal::create($data);

        // 🔔 Notify current user that a new deal was created
        $user->notify(new DealCreatedNotification($deal));

        return redirect()
            ->route('deals.index', ['workspace' => $ws->slug])
            ->with('status', 'Deal created successfully.');
    }

    public function edit(Deal $deal)
    {
        $ws = $this->ws();
        $this->authorizeDeal($deal, $ws);

        $pipelines = Pipeline::where('workspace_id', $ws->id)
            ->with(['stages' => fn($q) => $q->orderBy('position')])
            ->orderBy('name')
            ->get();

        $companies = Company::where('workspace_id', $ws->id)->orderBy('name')->get();
        $contacts  = Contact::where('workspace_id', $ws->id)->orderBy('first_name')->get();

        return view('deals.edit', compact('deal', 'pipelines', 'companies', 'contacts'));
    }

    public function update(Request $request, Deal $deal)
    {
        $ws = $this->ws();
        $this->authorizeDeal($deal, $ws);

        $data = $request->validate([
            'title'              => ['required', 'string', 'max:255'],
            'pipeline_id'        => ['required', 'integer'],
            'stage_id'           => ['required', 'integer'],
            'company_id'         => ['nullable', 'integer'],
            'primary_contact_id' => ['nullable', 'integer'],
            'amount'             => ['nullable', 'numeric'],
            'currency'           => ['nullable', 'string', 'size:3'],
            'status'             => ['nullable', 'string', 'max:20'],
            'expected_close_date'=> ['nullable', 'date'],
        ]);

        // ✅ Validate pipeline belongs to workspace
        $pipeline = Pipeline::where('workspace_id', $ws->id)
            ->where('id', $data['pipeline_id'])
            ->firstOrFail();

        // ✅ Validate stage belongs to pipeline + workspace
        $stage = Stage::where('workspace_id', $ws->id)
            ->where('pipeline_id', $pipeline->id)
            ->where('id', $data['stage_id'])
            ->firstOrFail();

        if (!empty($data['company_id'])) {
            Company::where('workspace_id', $ws->id)->where('id', $data['company_id'])->firstOrFail();
        }

        if (!empty($data['primary_contact_id'])) {
            Contact::where('workspace_id', $ws->id)->where('id', $data['primary_contact_id'])->firstOrFail();
        }

        $user = $request->user();
        $oldStatus = $deal->status;

        $deal->update($data);

        if ($oldStatus !== Deal::STATUS_WON && $deal->status === Deal::STATUS_WON) {
            $user->notify(new DealWonNotification($deal));
        }

        return redirect()
            ->route('deals.index', ['workspace' => $ws->slug])
            ->with('status', 'Deal updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        $ws = $this->ws();
        $this->authorizeDeal($deal, $ws);

        $deal->delete();

        return redirect()
            ->route('deals.index', ['workspace' => $ws->slug])
            ->with('status', 'Deal deleted.');
    }

    public function show(Deal $deal)
    {
        $ws = $this->ws();
        $this->authorizeDeal($deal, $ws);

        $deal->load([
            'company',
            'primaryContact',
            'stage',
            'activities.owner',
            'notes.user',
        ]);

        $timeline = $deal->activities->map(function ($activity) {
            return [
                'kind'  => 'activity',
                'date'  => $activity->due_date ?? $activity->created_at,
                'model' => $activity,
            ];
        })->merge(
            $deal->notes->map(function ($note) {
                return [
                    'kind'  => 'note',
                    'date'  => $note->created_at,
                    'model' => $note,
                ];
            })
        )->sortByDesc('date')->values();

        return view('deals.show', [
            'deal'     => $deal,
            'timeline' => $timeline,
        ]);
    }

    public function board(Request $request)
    {
        $ws = $this->ws();

        $pipelines = Pipeline::where('workspace_id', $ws->id)
            ->orderBy('name')
            ->get();

        $pipeline = null;
        if ($request->filled('pipeline_id')) {
            $pipeline = $pipelines->firstWhere('id', (int) $request->pipeline_id);
        }
        if (!$pipeline) {
            $pipeline = $pipelines->firstWhere('is_default', true) ?? $pipelines->first();
        }

        if (!$pipeline) {
            return redirect()
                ->route('pipelines.index', ['workspace' => $ws->slug])
                ->with('status', 'Please create a pipeline first.');
        }

        $status = $request->get('status', 'all');

        $pipeline->load(['stages' => fn($q) => $q->orderBy('position')]);

        $dealsQuery = Deal::where('workspace_id', $ws->id)
            ->where('pipeline_id', $pipeline->id)
            ->with(['company', 'primaryContact', 'stage'])
            ->orderByDesc('updated_at');

        if (in_array($status, ['open', 'won', 'lost'], true)) {
            $dealsQuery->where('status', $status);
        }

        $deals = $dealsQuery->get()->groupBy('stage_id');

        return view('deals.board', [
            'pipeline'     => $pipeline,
            'pipelines'    => $pipelines,
            'dealsByStage' => $deals,
            'status'       => $status,
        ]);
    }

    public function moveOnBoard(Request $request)
    {
        $ws = $this->ws();

        $data = $request->validate([
            'deal_id'  => ['required', 'integer'],
            'stage_id' => ['required', 'integer'],
        ]);

        $deal = Deal::where('workspace_id', $ws->id)
            ->where('id', $data['deal_id'])
            ->firstOrFail();

        $stage = Stage::where('workspace_id', $ws->id)
            ->where('id', $data['stage_id'])
            ->firstOrFail();

        if ($stage->pipeline_id !== $deal->pipeline_id) {
            return response()->json([
                'ok'      => false,
                'message' => 'Stage does not belong to the same pipeline.',
            ], 422);
        }

        $deal->stage_id = $stage->id;
        $deal->save();

        return response()->json([
            'ok'      => true,
            'message' => 'Deal moved.',
        ]);
    }

    protected function authorizeDeal(Deal $deal, $ws): void
    {
        if ((int) $deal->workspace_id !== (int) $ws->id) {
            abort(403);
        }
    }
}
