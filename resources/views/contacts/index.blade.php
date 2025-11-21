@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Contacts</h1>
  <a href="{{ route('contacts.create') }}" class="btn btn-primary">Add Contact</a>
</div>

@if(session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Company</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Stage</th>
          <th>Created</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($contacts as $contact)
        <tr>
          <td>{{ $contact->full_name }}</td>
          <td>{{ optional($contact->company)->name }}</td>
          <td>{{ $contact->email }}</td>
          <td>{{ $contact->phone }}</td>
          <td>{{ $contact->lifecycle_stage }}</td>
          <td>{{ $contact->created_at->format('Y-m-d') }}</td>
          <td class="text-end">
            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger"
                      onclick="return confirm('Delete this contact?')">
                Delete
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted">No contacts yet.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-body">
    {{ $contacts->links() }}
  </div>
</div>
@endsection
