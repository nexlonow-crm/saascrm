@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">All Notifications</h5>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Mark all as read</button>
            </form>
        @endif
    </div>
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            @php $data = $notification->data; @endphp
            <a href="{{ $data['url'] ?? '#' }}"
               class="list-group-item list-group-item-action d-flex justify-content-between">
                <div>
                    <div class="{{ is_null($notification->read_at) ? 'fw-bold' : '' }}">
                        {{ $data['title'] ?? 'Notification' }}
                    </div>
                    <div class="text-muted small">
                        {{ $data['body'] ?? '' }}
                    </div>
                </div>
                <div class="text-muted small">
                    {{ $notification->created_at->diffForHumans() }}
                </div>
            </a>
        @empty
            <div class="list-group-item text-center text-muted">
                No notifications yet.
            </div>
        @endforelse
    </div>
    <div class="card-body">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
