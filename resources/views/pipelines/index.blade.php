@extends('layouts.app')

@section('title', 'Pipelines')
@section('page-title', 'Pipelines')

@section('content')
@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Pipelines</h5>
        <a href="{{ route('pipelines.create') }}" class="btn btn-primary btn-sm">Add Pipeline</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Default</th>
                    <th>Stages</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pipelines as $pipeline)
                    <tr>
                        <td>{{ $pipeline->name }}</td>
                        <td>{{ $pipeline->type ?? '—' }}</td>
                        <td>
                            @if($pipeline->is_default)
                                <span class="badge bg-success">Default</span>
                            @endif
                        </td>
                        <td>{{ $pipeline->stages_count }}</td>
                        <td>{{ $pipeline->created_at->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <a href="{{ route('pipelines.edit', $pipeline) }}"
                               class="btn btn-sm btn-outline-secondary">
                                Manage
                            </a>
                            <form action="{{ route('pipelines.destroy', $pipeline) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this pipeline?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No pipelines yet. Create one to start tracking deals.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-body">
        {{ $pipelines->links() }}
    </div>
</div>
@endsection
