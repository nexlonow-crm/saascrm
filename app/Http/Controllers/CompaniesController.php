<?php

namespace App\Http\Controllers;

use App\Domain\Companies\Models\Company;
use Illuminate\Http\Request;


class CompaniesController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companies = Company::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->latest()
            ->paginate(15);
      
        return view('companies.index', [
            'companies' => $companies,
        ]);

    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

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

        $data['account_id'] = $user->account_id;
        $data['tenant_id']  = $user->tenant_id;
        $data['owner_id']   = $user->id;

        Company::create($data);

        return redirect()
            ->route('companies.index')
            ->with('status', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        $this->authorizeCompany($company);

        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $this->authorizeCompany($company);

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
            ->route('companies.index')
            ->with('status', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $this->authorizeCompany($company);

        $company->delete();

        return redirect()
            ->route('companies.index')
            ->with('status', 'Company deleted.');
    }

    protected function authorizeCompany(Company $company): void
    {
        $user = auth()->user();

        if ($company->account_id !== $user->account_id || $company->tenant_id !== $user->tenant_id) {
            abort(403);
        }
    }

    public function show(Company $company)
    {
        if (method_exists($this, 'authorizeCompany')) {
            $this->authorizeCompany($company);
        }

        $company->load([
            'contacts',
            'deals.stage',
            'activities.owner',
        ]);

        return view('companies.show', compact('company'));
    }
    
}
