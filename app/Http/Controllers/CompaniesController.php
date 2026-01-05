<?php

namespace App\Http\Controllers;

use App\Domain\Companies\Models\Company;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    private function ws()
    {
        return app('currentWorkspace');
    }

    public function index(Request $request)
    {
        $ws = $this->ws();

        $companies = Company::query()
            ->where('workspace_id', $ws->id) // ✅ always scope
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('companies.index', [
            'companies' => $companies,
            'workspace' => $ws,
        ]);
    }

    public function create()
    {
        $ws = $this->ws();

        return view('companies.create', [
            'workspace' => $ws,
        ]);
    }

    public function store(Request $request)
    {
        $ws = $this->ws();
        $user = $request->user();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'domain'      => ['nullable', 'string', 'max:255'],
            'website'     => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'industry'    => ['nullable', 'string', 'max:255'],
            'size'        => ['nullable', 'string', 'max:50'],
            'street'      => ['nullable', 'string', 'max:255'],
            'city'        => ['nullable', 'string', 'max:255'],
            'state'       => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country'     => ['nullable', 'string', 'max:255'],
        ]);

        // ✅ Required scoping fields
        $data['workspace_id'] = $ws->id;     // ✅ FIX
        $data['owner_id']     = $user->id;

        Company::create($data);

        return redirect()
            ->route('companies.index', ['workspace' => $ws]) // ✅ model -> slug
            ->with('status', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $ws = $this->ws();
        $this->authorizeCompany($company, $ws);

        $company->load([
            'contacts',
            'deals.stage',
            'activities.owner',
            'notes.user',
        ]);

        $timeline = collect()
            ->merge($company->activities->map(fn ($a) => [
                'kind' => 'activity',
                'date' => $a->due_date ?? $a->created_at,
                'model' => $a,
            ]))
            ->merge($company->notes->map(fn ($n) => [
                'kind' => 'note',
                'date' => $n->created_at,
                'model' => $n,
            ]))
            ->sortByDesc('date')
            ->values();

        return view('companies.show', [
            'company'   => $company,
            'timeline'  => $timeline,
            'workspace' => $ws,
        ]);
    }

    public function edit(Company $company)
    {
        $ws = $this->ws();
        $this->authorizeCompany($company, $ws);

        return view('companies.edit', [
            'company' => $company,
            'workspace' => $ws,
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $ws = $this->ws();
        $this->authorizeCompany($company, $ws);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'domain'      => ['nullable', 'string', 'max:255'],
            'website'     => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'industry'    => ['nullable', 'string', 'max:255'],
            'size'        => ['nullable', 'string', 'max:50'],
            'street'      => ['nullable', 'string', 'max:255'],
            'city'        => ['nullable', 'string', 'max:255'],
            'state'       => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country'     => ['nullable', 'string', 'max:255'],
        ]);

        $company->update($data);

        return redirect()
            ->route('companies.index', ['workspace' => $ws])
            ->with('status', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $ws = $this->ws();
        $this->authorizeCompany($company, $ws);

        $company->delete();

        return redirect()
            ->route('companies.index', ['workspace' => $ws])
            ->with('status', 'Company deleted.');
    }

    protected function authorizeCompany(Company $company, $ws): void
    {
        if ((int) $company->workspace_id !== (int) $ws->id) {
            abort(403);
        }
    }
}
