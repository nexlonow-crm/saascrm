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
use App\Models\Activity;




class DealsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $deals = Deal::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->with(['company', 'primaryContact', 'stage', 'pipeline'])
            ->latest()
            ->paginate(15);

        return view('deals.index', compact('deals'));
    }

    public function create()
    {
        $user = auth()->user();

        $pipelines = Pipeline::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->with('stages')
            ->get();

        $companies = Company::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('name')->get();

        $contacts = Contact::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('first_name')->get();

        return view('deals.create', compact('pipelines', 'companies', 'contacts'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'title'              => ['required', 'string', 'max:255'],
            'pipeline_id'        => ['required', 'exists:pipelines,id'],
            'stage_id'           => ['required', 'exists:stages,id'],
            'company_id'         => ['nullable', 'exists:companies,id'],
            'primary_contact_id' => ['nullable', 'exists:contacts,id'],
            'amount'             => ['nullable', 'numeric'],
            'currency'           => ['nullable', 'string', 'size:3'],
            'status'             => ['nullable', 'string', 'max:20'],
            'expected_close_date'=> ['nullable', 'date'],
        ]);

        $data['account_id'] = $user->account_id;
        $data['tenant_id']  = $user->tenant_id;
        $data['owner_id']   = $user->id;
        $data['currency']   = $data['currency'] ?? 'USD';
        $data['status']     = $data['status'] ?? Deal::STATUS_OPEN;

        $deal = Deal::create($data);
         
        // 🔔 Notify current user that a new deal was created
        $user->notify(new DealCreatedNotification($deal));

        return redirect()
            ->route('deals.index')
            ->with('status', 'Deal created successfully.');
    }

    public function edit(Deal $deal)
    {
        $this->authorizeDeal($deal);

        $user = auth()->user();

        $pipelines = Pipeline::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->with('stages')
            ->get();

        $companies = Company::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('name')->get();

        $contacts = Contact::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('first_name')->get();

        return view('deals.edit', compact('deal', 'pipelines', 'companies', 'contacts'));
    }

    public function update(Request $request, Deal $deal)
    {
        $this->authorizeDeal($deal);

        $data = $request->validate([
            'title'              => ['required', 'string', 'max:255'],
            'pipeline_id'        => ['required', 'exists:pipelines,id'],
            'stage_id'           => ['required', 'exists:stages,id'],
            'company_id'         => ['nullable', 'exists:companies,id'],
            'primary_contact_id' => ['nullable', 'exists:contacts,id'],
            'amount'             => ['nullable', 'numeric'],
            'currency'           => ['nullable', 'string', 'size:3'],
            'status'             => ['nullable', 'string', 'max:20'],
            'expected_close_date'=> ['nullable', 'date'],
        ]);

        $user = auth()->user();
        $oldStatus = $deal->status;
    
        $deal->update($data);
         // After update, check if status changed to WON
        if ($oldStatus !== Deal::STATUS_WON && $deal->status === Deal::STATUS_WON) {
            // 🔔 Notify current user that deal is won
            $user->notify(new DealWonNotification($deal));
        }



        return redirect()
            ->route('deals.index')
            ->with('status', 'Deal updated successfully.');
    }

    public function destroy(Deal $deal)
    {
        $this->authorizeDeal($deal);

        $deal->delete();

        return redirect()
            ->route('deals.index')
            ->with('status', 'Deal deleted.');
    }

    protected function authorizeDeal(Deal $deal): void
    {
        $user = auth()->user();

        if ($deal->account_id !== $user->account_id || $deal->tenant_id !== $user->tenant_id) {
            abort(403);
        }
    }
    


    public function show(Deal $deal)
    {
        $this->authorizeDeal($deal); // keep your existing auth

        $deal->load([
            'company',
            'primaryContact',
            'stage',
            'activities.owner',
            'notes.user',
        ]);

        // Build combined timeline: activities + notes
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
    $user = $request->user();

    // All pipelines for this account/tenant (for dropdown)
    $pipelines = Pipeline::where('account_id', $user->account_id)
        ->where('tenant_id', $user->tenant_id)
        ->orderBy('name')
        ->get();

    // Determine active pipeline: from query or default
    $pipeline = null;

    if ($request->filled('pipeline_id')) {
        $pipeline = $pipelines->firstWhere('id', (int) $request->pipeline_id);
    }

    if (!$pipeline) {
        $pipeline = $pipelines->firstWhere('is_default', true) ?? $pipelines->first();
    }

    if (!$pipeline) {
        return redirect()
            ->route('pipelines.index')
            ->with('status', 'Please create a pipeline first.');
    }

    // Status filter: all / open / won / lost
    $status = $request->get('status', 'all'); // default: all

    // Load stages for this pipeline
    $pipeline->load([
        'stages' => function ($q) {
            $q->orderBy('position');
        },
    ]);

    // Base query
    $dealsQuery = Deal::where('account_id', $user->account_id)
        ->where('tenant_id', $user->tenant_id)
        ->where('pipeline_id', $pipeline->id)
        ->with(['company', 'primaryContact', 'stage'])
        ->orderByDesc('updated_at');

    if (in_array($status, ['open', 'won', 'lost'], true)) {
        $dealsQuery->where('status', $status);
    }

    $deals = $dealsQuery->get()->groupBy('stage_id'); // stage_id => Collection<Deal>

    return view('deals.board', [
        'pipeline'     => $pipeline,
        'pipelines'    => $pipelines,
        'dealsByStage' => $deals,
        'status'       => $status,
    ]);
}



    public function moveOnBoard(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'deal_id'  => ['required', 'integer'],
            'stage_id' => ['required', 'integer'],
        ]);

        $deal = Deal::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->where('id', $data['deal_id'])
            ->firstOrFail();

        // $stage = \App\Domain\Deals\Models\Stage::where('account_id', $user->account_id)
        //     ->where('tenant_id', $user->tenant_id)
        //     ->where('id', $data['stage_id'])
        //     ->firstOrFail();

        $stage = Stage::where('id', $data['stage_id'])->firstOrFail();
                        

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





}
