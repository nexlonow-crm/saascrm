<form method="POST" action="{{ route('notes.store') }}" class="mb-3">
    @csrf

    <input type="hidden" name="subject_type" value="{{ get_class($subject) }}">
    <input type="hidden" name="subject_id" value="{{ $subject->id }}">

    <div class="mb-2">
        <label class="form-label">Add note</label>
        <textarea name="body" class="form-control" rows="2" required
                  placeholder="Write a note..."></textarea>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1"
                   id="pin-note-{{ $subject->id }}" name="is_pinned">
            <label class="form-check-label" for="pin-note-{{ $subject->id }}">
                Pin this note
            </label>
        </div>
        <button class="btn btn-sm btn-primary">Save note</button>
    </div>
</form>
