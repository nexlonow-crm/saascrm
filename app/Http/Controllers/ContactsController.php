<?php

namespace App\Http\Controllers;

use App\Domain\Contacts\Models\Contact;
use App\Domain\Companies\Models\Company;
use Illuminate\Http\Request;
use App\Notifications\ContactCreatedNotification;

class ContactsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $contacts = Contact::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->with('company')
            ->latest()
            ->paginate(15);

        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        $user = auth()->user();

        $companies = Company::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get();

        return view('contacts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'company_id'      => ['nullable', 'exists:companies,id'],
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

        $data['account_id'] = $user->account_id;
        $data['tenant_id']  = $user->tenant_id;
        $data['owner_id']   = $user->id;
        $data['status']     = $data['status'] ?? 'active';

        
        $contact = Contact::create($data);

        // notify the current user (or account owner etc.)
        $user->notify(new ContactCreatedNotification($contact));
        

        return redirect()
            ->route('contacts.index')
            ->with('status', 'Contact created successfully.');
    }

    public function edit(Contact $contact)
    {
        $this->authorizeContact($contact);

        $user = auth()->user();

        $companies = Company::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('name')
            ->get();

        return view('contacts.edit', compact('contact', 'companies'));
    }

    public function update(Request $request, Contact $contact)
    {
        $this->authorizeContact($contact);

        $data = $request->validate([
            'company_id'      => ['nullable', 'exists:companies,id'],
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

        $contact->update($data);

        return redirect()
            ->route('contacts.index')
            ->with('status', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $this->authorizeContact($contact);

        $contact->delete();

        return redirect()
            ->route('contacts.index')
            ->with('status', 'Contact deleted.');
    }

    protected function authorizeContact(Contact $contact): void
    {
        $user = auth()->user();

        if ($contact->account_id !== $user->account_id || $contact->tenant_id !== $user->tenant_id) {
            abort(403);
        }
    }
}
