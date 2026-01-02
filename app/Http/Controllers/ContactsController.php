<?php

namespace App\Http\Controllers;

use App\Domain\Contacts\Models\Contact;
use App\Domain\Companies\Models\Company;
use Illuminate\Http\Request;
use App\Notifications\ContactCreatedNotification;

class ContactsController extends Controller
{
    private function ws()
    {
        return app('currentWorkspace'); // set by SetWorkspace middleware
    }

    public function index()
    {
        $ws = $this->ws();

        $contacts = Contact::where('workspace_id', $ws->id)
            ->with('company')
            ->latest()
            ->paginate(15);

        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $ws = $this->ws();

        $companies = Company::where('workspace_id', $ws->id)
            ->orderBy('name')
            ->get();

        return view('contacts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $ws = $this->ws();
        $user = $request->user();

        $data = $request->validate([
            'company_id'      => ['nullable', 'integer'],
            'first_name'      => ['required', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'email'           => ['nullable', 'email'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'mobile'          => ['nullable', 'string', 'max:50'],
            'job_title'       => ['nullable', 'string', 'max:255'],
            'street'          => ['nullable', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'state'           => ['nullable', 'string', 'max:255'],
            'postal_code'     => ['nullable', 'string', 'max:50'],
            'country'         => ['nullable', 'string', 'max:255'],
            'lifecycle_stage' => ['nullable', 'string', 'max:50'],
            'lead_source'     => ['nullable', 'string', 'max:255'],
            'status'          => ['nullable', 'string', 'max:50'],
        ]);

        // ✅ company must belong to workspace
        if (!empty($data['company_id'])) {
            Company::where('workspace_id', $ws->id)
                ->where('id', $data['company_id'])
                ->firstOrFail();
        }

        $data['workspace_id'] = $ws->id;
        $data['owner_id']     = $user->id;
        $data['status']       = $data['status'] ?? 'active';

        $contact = Contact::create($data);

        $user->notify(new ContactCreatedNotification($contact));

        return redirect()
            ->route('contacts.index', ['workspace' => $ws->slug])
            ->with('status', 'Contact created successfully.');
    }

    public function edit(Contact $contact)
    {
        $ws = $this->ws();
        $this->authorizeContact($contact, $ws);

        $companies = Company::where('workspace_id', $ws->id)
            ->orderBy('name')
            ->get();

        return view('contacts.edit', compact('contact', 'companies'));
    }

    public function update(Request $request, Contact $contact)
    {
        $ws = $this->ws();
        $this->authorizeContact($contact, $ws);

        $data = $request->validate([
            'company_id'      => ['nullable', 'integer'],
            'first_name'      => ['required', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'email'           => ['nullable', 'email'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'mobile'          => ['nullable', 'string', 'max:50'],
            'job_title'       => ['nullable', 'string', 'max:255'],
            'street'          => ['nullable', 'string', 'max:255'],
            'city'            => ['nullable', 'string', 'max:255'],
            'state'           => ['nullable', 'string', 'max:255'],
            'postal_code'     => ['nullable', 'string', 'max:50'],
            'country'         => ['nullable', 'string', 'max:255'],
            'lifecycle_stage' => ['nullable', 'string', 'max:50'],
            'lead_source'     => ['nullable', 'string', 'max:255'],
            'status'          => ['nullable', 'string', 'max:50'],
        ]);

        // ✅ company must belong to workspace
        if (!empty($data['company_id'])) {
            Company::where('workspace_id', $ws->id)
                ->where('id', $data['company_id'])
                ->firstOrFail();
        }

        $contact->update($data);

        return redirect()
            ->route('contacts.index', ['workspace' => $ws->slug])
            ->with('status', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $ws = $this->ws();
        $this->authorizeContact($contact, $ws);

        $contact->delete();

        return redirect()
            ->route('contacts.index', ['workspace' => $ws->slug])
            ->with('status', 'Contact deleted.');
    }

    protected function authorizeContact(Contact $contact, $ws): void
    {
        if ((int) $contact->workspace_id !== (int) $ws->id) {
            abort(403);
        }
    }

    public function show(Contact $contact)
    {
        $ws = $this->ws();
        $this->authorizeContact($contact, $ws);

        $contact->load([
            'company',
            'deals.stage',
            'activities.owner',
            'notes.user',
        ]);

        $timeline = $contact->activities->map(function ($activity) {
            return [
                'kind'  => 'activity',
                'date'  => $activity->due_date ?? $activity->created_at,
                'model' => $activity,
            ];
        })->merge(
            $contact->notes->map(function ($note) {
                return [
                    'kind'  => 'note',
                    'date'  => $note->created_at,
                    'model' => $note,
                ];
            })
        )->sortByDesc('date')->values();

        return view('contacts.show', [
            'contact'  => $contact,
            'timeline' => $timeline,
        ]);
    }
}
