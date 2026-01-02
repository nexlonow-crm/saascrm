@extends('layouts.app')

@section('title', 'Edit Pipeline')

@section('page-title', 'Edit Pipeline')

@section('content')
<div class="row">
    <div class="col-lg-7">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pipeline Details</h5>
                <a href="{{ route('pipelines.index', ['workspace' => $ws->slug]) }}" class="btn btn-outline-secondary btn-sm">
                    Back
                </a>
            </div>

            <div class="card-body">
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-bold mb-1">Please fix the errors:</div>
                        <ul class="mb-0">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('pipelines.update', ['workspace' => $ws->slug, 'pipeline' => $pipeline->id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Pipeline Name</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $pipeline->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type (optional)</label>
                        <input type="text"
                               name="type"
                               value="{{ old('type', $pipeline->type) }}"
                               class="form-control @error('type') is-invalid @enderror"
                               placeholder="sales / job_search / personal">
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input"
                               type="checkbox"
                               name="is_default"
                               id="is_default"
                               value="1"
                               {{ old('is_default', $pipeline->is_default) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_default">
                            Set as default pipeline for this workspace
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Save Changes</button>

                        <form method="POST"
                              action="{{ route('pipelines.destroy', ['workspace' => $ws->slug, 'pipeline' => $pipeline->id]) }}"
                              class="d-inline"
                              onsubmit="return confirm('Delete this pipeline?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </form>
            </div>
        </div>

        {{-- Stages --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Stages</h5>
            </div>

            <div class="card-body">
                @if($pipeline->stages->count() === 0)
                    <div class="text-muted">No stages yet. Add your first stage below.</div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Name</th>
                                    <th style="width: 150px;">Probability</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pipeline->stages as $stage)
                                    <tr>
                                        <td class="text-muted">{{ $stage->position }}</td>
                                        <td class="fw-semibold">{{ $stage->name }}</td>
                                        <td>{{ $stage->probability ?? '-' }}%</td>
                                        <td class="text-end">
                                            {{-- Update stage (simple inline form) --}}
                                            <button class="btn btn-sm btn-outline-primary"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#editStage{{ $stage->id }}">
                                                Edit
                                            </button>

                                            <form method="POST"
                                                action="{{ route('pipelines.stages.destroy', ['workspace' => $ws->slug, 'pipeline' => $pipeline->id, 'stage' => $stage->id]) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Delete this stage?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>

                                            <div class="collapse mt-2" id="editStage{{ $stage->id }}">
                                                <form method="POST"
                                                      action="{{ route('pipelines.stages.update', ['workspace' => $ws->slug, 'pipeline' => $pipeline->id, 'stage' => $stage->id]) }}"
                                                      class="border rounded p-3 bg-light">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <input type="text"
                                                                name="name"
                                                                value="{{ old('name', $stage->name) }}"
                                                                class="form-control"
                                                                placeholder="Stage name"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="number"
                                                                name="probability"
                                                                value="{{ old('probability', $stage->probability) }}"
                                                                class="form-control"
                                                                min="0" max="100"
                                                                placeholder="%">
                                                        </div>
                                                        <div class="col-md-3 d-grid">
                                                            <button class="btn btn-primary btn-sm">Update</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- If you already have stage reorder JS, keep it.
                         Otherwise we can add later. --}}
                @endif

                <hr class="my-4">

                {{-- Add Stage --}}
                <h6 class="mb-2">Add Stage</h6>
                <form method="POST" action="{{ route('pipelines.stages.store', ['workspace' => $ws->slug, 'pipeline' => $pipeline->id]) }}">
                    @csrf

                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Proposal Sent"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <input type="number"
                                   name="probability"
                                   value="{{ old('probability') }}"
                                   class="form-control @error('probability') is-invalid @enderror"
                                   min="0" max="100"
                                   placeholder="Probability %">
                            @error('probability')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 d-grid">
                            <button class="btn btn-success">Add Stage</button>
                        </div>
                    </div>

                    <div class="form-text mt-2">
                        Tip: Keep “Won” at 100% and “Lost” at 0%.
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
