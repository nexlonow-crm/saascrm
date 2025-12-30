@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Deals</h1>
  <a href="{{ ws_route('deals.create') }}" class="btn btn-primary">Add Deal</a>
</div>

@if(session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Title</th>
          <th>Company</th>
          <th>Primary Contact</th>
          <th>Pipeline / Stage</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Expected Close</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($deals as $deal)
        <tr>
          <td>
             <a href="{{ ws_route('deals.show', $deal) }}">
                 {{ $deal->title }}
            </a>
         
        
          </td>
          <td>{{ optional($deal->company)->name }}</td>
          <td>{{ optional($deal->primaryContact)->full_name }}</td>
          <td>
            {{ optional($deal->pipeline)->name }}
             @if($deal->stage)
              <span class="badge bg-{{ $deal->stage->badgeColor() }}">
                  {{ $deal->stage->displayName() }}
              </span>
          @endif
          </td>
          <td>
            @if(!is_null($deal->amount))
              {{ $deal->currency }} {{ number_format($deal->amount, 2) }}
            @endif
          </td>
          <td>
            @php
              $statusClass = match($deal->status) {
                  \App\Domain\Deals\Models\Deal::STATUS_WON => 'success',
                  \App\Domain\Deals\Models\Deal::STATUS_LOST => 'danger',
                  default => 'secondary',
              };
            @endphp
            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($deal->status) }}</span>
          </td>
          <td>{{ optional($deal->expected_close_date)->format('Y-m-d') }}</td>
          <td class="text-end">
            <a href="{{ ws_route('deals.edit', $deal) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            <form action="{{ ws_route('deals.destroy', $deal) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger"
                      onclick="return confirm('Delete this deal?')">
                Delete
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted">No deals yet.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-body">
    {{ $deals->links() }}
  </div>
</div>
@endsection
