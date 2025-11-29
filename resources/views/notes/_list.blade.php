@if($subject->notes->count())
    <ul class="list-group">
        @foreach($subject->notes as $note)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        @if($note->is_pinned)
                            <span class="badge bg-warning text-dark me-1">Pinned</span>
                        @endif
                        <span class="text-muted small">
                            {{ $note->user->name ?? 'User' }}
                            • {{ $note->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <form method="POST"
                          action="{{ route('notes.destroy', $note) }}"
                          onsubmit="return confirm('Delete this note?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            Delete
                        </button>
                    </form>
                </div>
                <div class="mt-1">
                    {!! nl2br(e($note->body)) !!}
                </div>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-muted mb-0">No notes yet.</p>
@endif
