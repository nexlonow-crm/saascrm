<?php

namespace App\Http\Controllers;

use App\Domain\Companies\Models\Company;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function index(Request $request)
    {
        $workspace = app('currentWorkspace');

        $companies = Company::query()
            // If you use BelongsToWorkspace global scope, this is already scoped.
            ->when(!method_exists(Company::class, 'bootBelongsToWorkspace'), function ($q) use ($workspace) {
                $q->where('workspace_id', $workspace->id);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('companies.index', [
            'companies' => $companies,
            'workspace' => $workspace,
        ]);
    }

    public function create()
    {
        $workspace = app('currentWorkspace');

        return view('companies.create', [
            'workspace' => $workspace,
        ]);
    }

    public function store(Request $request)
    {
        $workspace = app('currentWorkspace');
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

        // Required scoping fields
        $data['workspace_id'] = $workspace->id;
        $data['owner_id'] = $user->id;

        $company = Company::create($data);

        return redirect()
            ->route('companies.index', ['workspace' => $workspace->id])
            ->with('status', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $workspace = app('currentWorkspace');

        $this->authorizeCompany($company, $workspace);

        $company->load([
            'contacts',
            'deals.stage',
            'activities.owner',
            'notes.user',
        ]);

        // Combined timeline: activities + notes
        $timeline = collect();

        foreach ($company->activities as $activity) {
            $timeline->push([
                'kind'  => 'activity',
                'date'  => $activity->due_date ?? $activity->created_at,
                'model' => $activity,
            ]);
        }

        foreach ($company->notes as $note) {
            $timeline->push([
                'kind'  => 'note',
                'date'  => $note->created_at,
                'model' => $note,
            ]);
        }

        $timeline = $timeline->sortByDesc('date')->values();

        return view('companies.show', [
            'company'  => $company,
            'timeline' => $timeline,
            'workspace' => $workspace,
        ]);
    }

    public function edit(Company $company)
    {
        $workspace = app('currentWorkspace');

        $this->authorizeCompany($company, $workspace);

        return view('companies.edit', [
            'company' => $company,
            'workspace' => $workspace,
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $workspace = app('currentWorkspace');

        $this->authorizeCompany($company, $workspace);

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
            ->route('companies.index', ['workspace' => $workspace->id])
            ->with('status', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $workspace = app('currentWorkspace');

        $this->authorizeCompany($company, $workspace);

        $company->delete();

        return redirect()
            ->route('companies.index', ['workspace' => $workspace->id])
            ->with('status', 'Company deleted.');
    }

    /**
     * Workspace-level authorization:
     * - If you have BelongsToWorkspace global scope, Laravel binding will already prevent cross-workspace access.
     * - This is still good to keep as a safety net.
     */
    protected function authorizeCompany(Company $company, $workspace): void
    {
        if ((int) $company->workspace_id !== (int) $workspace->id) {
            abort(403);
        }
    }
}
