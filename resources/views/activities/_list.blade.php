@if($subject->activities->count())
    <ul class="list-group mb-3">
        @foreach($subject->activities as $activity)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <div class="mb-1">
                        <span class="badge bg-secondary me-2">
                            {{ ucfirst($activity->type) }}
                        </span>
                        <strong>{{ $activity->title }}</strong>
                    </div>
                    @if($activity->due_date)
                        <div class="text-muted small">
                            Due: {{ $activity->due_date->format('Y-m-d H:i') }}
                        </div>
                    @endif
                    @if($activity->notes)
                        <div class="small mt-1">
                            {{ Str::limit($activity->notes, 120) }}
                        </div>
                    @endif
                </div>

                <div class="text-end">
                    {{-- Simple complete toggle (optional later) --}}
                    {{-- Delete --}}
                    <form method="POST"
                          action="{{ route('activities.destroy', $activity) }}"
                          onsubmit="return confirm('Delete this activity?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            Delete
                        </button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-muted mb-0">No activities yet.</p>
@endif
