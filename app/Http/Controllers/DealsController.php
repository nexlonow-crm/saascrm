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
}
