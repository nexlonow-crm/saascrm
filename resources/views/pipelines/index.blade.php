@extends('layouts.app')

@section('title', 'Pipelines')

@section('page-title', 'Pipelines')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pipelines</h5>

                <a href="{{ route('pipelines.create', ['workspace' => $ws->slug]) }}" class="btn btn-primary btn-sm">
                    + New Pipeline
                </a>
            </div>

            <div class="card-body">
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if($pipelines->count() === 0)
                    <div class="text-muted">No pipelines yet. Create your first pipeline.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th class="text-center">Stages</th>
                                    <th class="text-center">Default</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pipelines as $pipeline)
                                    <tr>
                                        <td class="fw-semibold">{{ $pipeline->name }}</td>
                                        <td class="text-muted">{{ $pipeline->type ?? '-' }}</td>
                                        <td class="text-center">{{ $pipeline->stages_count }}</td>
                                        <td class="text-center">
                                            @if($pipeline->is_default)
                                                <span class="badge bg-success">Default</span>
                                            @else
                                                <span class="badge bg-light text-dark">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="{{ route('pipelines.edit', ['workspace' => $ws->slug, 'pipeline' => $pipeline->id]) }}">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('pipelines.destroy', ['workspace' => $ws->slug, 'pipeline' => $pipeline->id]) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this pipeline?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $pipelines->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
