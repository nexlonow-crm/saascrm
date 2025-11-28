<form method="POST" action="{{ route('activities.store') }}" class="row g-2 mb-3">
    @csrf

    <input type="hidden" name="subject_type" value="{{ get_class($subject) }}">
    <input type="hidden" name="subject_id" value="{{ $subject->id }}">

    <div class="col-md-3">
        <select name="type" class="form-select">
            <option value="task">Task</option>
            <option value="call">Call</option>
            <option value="meeting">Meeting</option>
            <option value="email">Email</option>
        </select>
    </div>
    
    <div class="col-md-4">
        <input type="text" name="title" class="form-control" placeholder="Activity title" required>
    </div>

    <div class="col-md-3">
        <input type="datetime-local" name="due_date" class="form-control">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">Add</button>
    </div>
</form>
