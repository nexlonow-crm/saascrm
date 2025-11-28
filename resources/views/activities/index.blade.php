@extends('layouts.app')

@section('title', 'Activities')
@section('page-title', 'My Activities')

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Your Activities</h5>
    </div>
    <div class="card-body">

        @if($activities->count())
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Due Date</th>
                        <th>Related To</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($activities as $activity)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($activity->type) }}</span>
                        </td>
                        <td>{{ $activity->title }}</td>
                        <td>{{ $activity->due_date }}</td>
                        <td>
                            @if($activity->subject)
                                {{ class_basename($activity->subject_type) }}:
                                <strong>{{ $activity->subject->title ?? $activity->subject->name ?? '—' }}</strong>
                            @endif
                        </td>
                        <td class="text-end">
                            <form method="POST"
                                action="{{ route('activities.destroy', $activity) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        @else
            <p class="text-muted mb-0">No activities yet.</p>
        @endif

    </div>
</div>
@endsection
