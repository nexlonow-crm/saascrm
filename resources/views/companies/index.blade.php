@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Companies</h1>
  <a href="{{ route('companies.create') }}" class="btn btn-primary">Add Contact</a>
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
          <th>Domain</th>
          <th>Website</th>
          <th>Phone</th>
          <th>industry</th>
          <th>size</th>
          <th>street</th>
          <th>city</th>
          <th>state</th>
          <th>postal_code</th>
          <th>country</th>
          <th>extra</th>          
          <th>Created</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($companies as $companie)
        <tr>
          <td>
            <a href="{{ route('companies.show', $companie) }}">
                {{ $companie->name }}
            </a>
          </td>
          <td>{{ $companie->domain }}</td>
          <td>{{ $companie->website }}</td>
          <td>{{ $companie->phone }}</td>
          <td>{{ $companie->industry }}</td>
          <td>{{ $companie->size }}</td>
          <td>{{ $companie->street }}</td>
          <td>{{ $companie->city }}</td>
          <td>{{ $companie->state }}</td>
          <td>{{ $companie->postal_code }}</td>
          <td>{{ $companie->country }}</td>
          <td>{{ $companie->extra }}</td>    
          <td>{{ $companie->created_at->format('Y-m-d') }}</td>
          <td class="text-end">

            
            <a href="{{ route('companies.edit', $companie) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form action="{{ route('companies.destroy', $companie) }}" method="POST" class="d-inline">
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
    {{ $companies->links() }}
  </div>
</div>
@endsection
