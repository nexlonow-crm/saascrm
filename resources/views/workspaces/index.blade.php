@extends('layouts.app')

@section('title', 'Workspaces')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Your Workspaces</h4>
                <a class="btn btn-primary" href="{{ route('workspaces.create') }}">Create Workspace</a>
            </div>
            <div class="card-body">
                @forelse($workspaces as $ws)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <div class="fw-bold">{{ $ws->name }}</div>
                            <div class="text-muted small">{{ $ws->slug }}</div>
                        </div>
                        <a class="btn btn-sm btn-outline-primary"
                           href="{{ route('dashboard', ['workspace' => $ws->slug]) }}">
                            Open
                        </a>
                    </div>
                @empty
                    <div class="text-muted">No workspaces yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
